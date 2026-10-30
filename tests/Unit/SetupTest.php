<?php

namespace Tests\Unit;

use Illuminate\Filesystem\Filesystem;
use PDO;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class SetupTest extends TestCase
{
    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir().'/tech-solutions-setup-'.bin2hex(random_bytes(6));
        $files = new Filesystem;
        $root = dirname(__DIR__, 2);

        foreach (['app', 'bootstrap', 'config', 'routes', 'database/migrations', 'scripts'] as $directory) {
            $files->copyDirectory($root.'/'.$directory, $this->directory.'/'.$directory);
        }

        $files->cleanDirectory($this->directory.'/bootstrap/cache');
        $files->makeDirectory($this->directory.'/vendor');
        $files->makeDirectory($this->directory.'/storage/logs', 0755, true);
        $files->put($this->directory.'/vendor/autoload.php', '<?php return require '.var_export($root.'/vendor/autoload.php', true).';');
        $files->copy($root.'/.env.example', $this->directory.'/.env.example');
    }

    protected function tearDown(): void
    {
        (new Filesystem)->deleteDirectory($this->directory);
    }

    public function test_setup_can_be_repeated_without_replacing_configuration_or_data(): void
    {
        $first = $this->runSetup();
        $this->assertSame(0, $first->getExitCode(), $first->getErrorOutput().$first->getOutput());
        $environmentHash = hash_file('sha256', $this->directory.'/.env');
        $environment = \Dotenv\Dotenv::parse(file_get_contents($this->directory.'/.env'));
        $this->assertNotEmpty($environment['APP_KEY']);

        $database = new PDO('sqlite:'.$this->directory.'/database/database.sqlite');
        $database->exec("CREATE TABLE setup_sentinel (value TEXT); INSERT INTO setup_sentinel VALUES ('preserved')");

        $second = $this->runSetup();
        $this->assertSame(0, $second->getExitCode(), $second->getErrorOutput().$second->getOutput());
        $this->assertSame($environmentHash, hash_file('sha256', $this->directory.'/.env'));
        $this->assertSame('preserved', $database->query('SELECT value FROM setup_sentinel')->fetchColumn());
        $database = null;

        $existing = str_replace('APP_KEY='.$environment['APP_KEY'], 'APP_KEY=0', file_get_contents($this->directory.'/.env'));
        file_put_contents($this->directory.'/.env', $existing);
        $third = $this->runSetup();
        $this->assertSame(0, $third->getExitCode(), $third->getErrorOutput().$third->getOutput());
        $this->assertSame($existing, file_get_contents($this->directory.'/.env'));
    }

    public function test_setup_rejects_unsafe_or_incomplete_configuration_without_creating_a_database(): void
    {
        $example = file_get_contents($this->directory.'/.env.example');

        foreach ([
            str_replace('APP_ENV=local', 'APP_ENV=production', $example),
            str_replace('DB_CONNECTION=sqlite', 'DB_CONNECTION=mysql', $example),
            preg_replace('/^APP_KEY=.*\R/m', '', $example),
        ] as $environment) {
            file_put_contents($this->directory.'/.env', $environment);
            $result = $this->runSetup();
            $this->assertSame(1, $result->getExitCode(), $result->getOutput());
            $this->assertSame($environment, file_get_contents($this->directory.'/.env'));
            $this->assertFileDoesNotExist($this->directory.'/database/database.sqlite');
        }
    }

    private function runSetup(): Process
    {
        $process = new Process([PHP_BINARY, 'scripts/setup.php'], $this->directory, [
            'APP_ENV' => false,
            'APP_KEY' => false,
            'DB_CONNECTION' => false,
            'DB_DATABASE' => false,
            'DB_URL' => false,
            'APP_CONFIG_CACHE' => false,
        ]);
        $process->run();

        return $process;
    }
}

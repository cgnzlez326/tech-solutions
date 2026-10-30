<?php

use Dotenv\Dotenv;
use Illuminate\Contracts\Console\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';

try {
    $root = dirname(__DIR__);

    if (! is_file($root.'/.env') && ! copy($root.'/.env.example', $root.'/.env')) {
        throw new RuntimeException('No se pudo crear .env desde .env.example.');
    }

    $app = require $root.'/bootstrap/app.php';

    if ($app->configurationIsCached()) {
        throw new RuntimeException('Primero ejecuta php artisan config:clear para revisar la configuración local.');
    }

    $kernel = $app->make(Kernel::class);
    $kernel->bootstrap();

    if (! $app->environment('local')) {
        throw new RuntimeException('El setup automático solo admite APP_ENV=local.');
    }

    $database = $app->databasePath('database.sqlite');

    if (config('database.default') !== 'sqlite'
        || config('database.connections.sqlite.url')
        || config('database.connections.sqlite.database') !== $database) {
        throw new RuntimeException('El setup automático solo prepara SQLite local predeterminado. Revisa README.md para otra base.');
    }

    $run = function (string $command) use ($kernel): void {
        $status = $kernel->call($command, ['--no-interaction' => true]);
        echo $kernel->output();

        if ($status !== 0) {
            throw new RuntimeException("Falló el comando $command.");
        }
    };

    if (config('app.key') === null || config('app.key') === '') {
        $run('key:generate');
        $environment = Dotenv::parse(file_get_contents($app->environmentFilePath()));

        if (empty($environment['APP_KEY'])) {
            throw new RuntimeException('No se generó APP_KEY. Comprueba que exista una línea APP_KEY= en .env.');
        }
    }

    if (! is_file($database) && ! touch($database)) {
        throw new RuntimeException('No se pudo crear la base SQLite local.');
    }

    $run('migrate');
    echo "Backend preparado. Composer continuará con la instalación y compilación del frontend.\n";
} catch (Throwable $exception) {
    fwrite(STDERR, 'Setup: '.$exception->getMessage().PHP_EOL);
    exit(1);
}

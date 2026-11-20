<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicados = DB::table('users')->selectRaw('lower(email)')
            ->groupByRaw('lower(email)')
            ->havingRaw('count(*) > 1')
            ->exists();

        if ($duplicados) {
            throw new RuntimeException('Resuelve los correos de usuario duplicados antes de aplicar esta migración.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('email_normalized')->virtualAs('lower(email)')->unique();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['email_normalized']);
            $table->dropColumn('email_normalized');
        });
    }
};

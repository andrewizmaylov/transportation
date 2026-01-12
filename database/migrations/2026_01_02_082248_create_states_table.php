<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $sql = file_get_contents(database_path('seeders/Data/states.sql'));
        DB::unprepared($sql);
    }

    public function down(): void
    {
        DB::statement('DROP TABLE IF EXISTS states');
    }
};

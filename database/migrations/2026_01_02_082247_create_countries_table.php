<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $sql = file_get_contents(database_path('seeders/Data/countries.sql'));
        DB::unprepared($sql);
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};

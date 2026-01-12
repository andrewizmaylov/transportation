<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transportation_addresses', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('alias')->nullable();
            $table->uuid('client_id')->nullable();
            $table->string('type')->nullable();
            $table->string('contact')->nullable();
            $table->unsignedMediumInteger('city_id')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_line_3')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('comment')->nullable();
            $table->unsignedMediumInteger('country_id')->nullable();

            $table->foreign('client_id')->references('id')->on('users')->nullOnDelete();
            $table->foreign('city_id')->references('id')->on('cities')->onDelete('cascade');
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transportation_addresses');
    }
};

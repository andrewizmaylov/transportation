<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cargos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('transportation_id')->nullable();
            $table->uuid('client_id');

            $table->string('name');
            $table->unsignedSmallInteger('length');
            $table->unsignedSmallInteger('width');
            $table->unsignedSmallInteger('height');
            $table->unsignedSmallInteger('weight');
            $table->unsignedSmallInteger('price');
            $table->string('currency');

            $table->foreign('transportation_id')->references('id')->on('transportations')->nullOnDelete();
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cargos');
    }
};

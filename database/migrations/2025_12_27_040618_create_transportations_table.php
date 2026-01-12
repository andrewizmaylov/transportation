<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Src\Transportation\DomainLayer\Enum\TransportationStatus;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transportations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('client_id')->nullable()->index();
            $table->string('name')->nullable();
            $table->dateTime('pickup_from');
            $table->dateTime('pickup_to');
            $table->uuid('pickup_address_id')->nullable();
            $table->uuid('delivery_address_id')->nullable();
            $table->string('transportation_status')->default(TransportationStatus::NEW->value);

            $table->foreign('client_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transportations');
    }
};

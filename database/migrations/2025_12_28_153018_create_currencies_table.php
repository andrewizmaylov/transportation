<?php

declare(strict_types=1);

use App\Models\Currency;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('symbol');
            $table->unsignedSmallInteger('numeric_code');
            $table->timestamps();
        });

        Currency::create([
            'code' => 'USD',
            'symbol' => '$',
            'numeric_code' => '840',
        ]);

        Currency::create([
            'code' => 'EUR',
            'symbol' => '€',
            'numeric_code' => 978,
        ]);

        Currency::create([
            'code' => 'RUB',
            'symbol' => '₽',
            'numeric_code' => '643',
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};

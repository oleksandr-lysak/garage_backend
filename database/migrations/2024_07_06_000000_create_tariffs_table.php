<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tariffs', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->decimal('price', 8, 2)->nullable();
            $table->json('features')->nullable();
            $table->timestamps();
        });

        // Seed basic tariffs (free, premium) for initial use
        DB::table('tariffs')->insert([
            ['name' => 'free', 'price' => 0],
            ['name' => 'premium', 'price' => null],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('tariffs');
    }
};

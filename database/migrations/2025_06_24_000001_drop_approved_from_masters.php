<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            if (Schema::hasColumn('masters', 'approved')) {
                $table->dropColumn('approved');
            }
        });
    }

    public function down(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            $table->boolean('approved')->default(false);
        });
    }
};

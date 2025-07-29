<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            if (! Schema::hasColumn('masters', 'place_id')) {
                $table->string('place_id')->nullable()->unique()->after('id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            if (Schema::hasColumn('masters', 'place_id')) {
                $table->dropUnique(['place_id']);
                $table->dropColumn('place_id');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            if (Schema::hasColumn('masters', 'phone')) {
                $table->renameColumn('phone', 'contact_phone');
            }
            $table->string('contact_phone')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            if (Schema::hasColumn('masters', 'contact_phone')) {
                $table->renameColumn('contact_phone', 'phone');
                $table->string('phone')->nullable(false)->change();
            }
        });
    }
};

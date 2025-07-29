<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            if (! Schema::hasColumn('masters', 'user_id')) {
                // ensure column exists (just in case)
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            $table->index('user_id', 'masters_user_id_index');
            if (Schema::hasColumn('masters', 'contact_phone')) {
                $table->index('contact_phone', 'masters_contact_phone_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('masters', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            if (Schema::hasColumn('masters', 'contact_phone')) {
                $table->dropIndex(['contact_phone']);
            }
        });
    }
};

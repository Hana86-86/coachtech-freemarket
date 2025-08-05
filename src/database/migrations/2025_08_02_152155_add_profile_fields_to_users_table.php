<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone',20)->nullable();
            $table->string('postal_code', 20)->nullable();
            $table->text('address');
            $table->string('building',255)->nullable();
            $table->string('profile_image',255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'postal_code', 'address', 'building', 'profile_image']);
        });
    }
};

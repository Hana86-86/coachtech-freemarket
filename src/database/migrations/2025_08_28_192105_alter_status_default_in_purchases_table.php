<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('purchases')->where('status', 'paid')->update(['status' => 'trading']);

        Schema::table('purchases', function (Blueprint $table) {
            $table->string('status', 20)->default('trading')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->string('status', 20)->default('paid')->change();
        });

        DB::table('purchases')->where('status', 'trading')->update(['status' => 'paid']);
    }
};

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
        Schema::table('purchases', function (Blueprint $table) {
            $table->timestamp('buyer_last_read_at')
                ->nullable()
                ->comment('購入者の最終既読日時')
                ->after('buyer_rating');

            $table->timestamp('seller_last_read_at')
                ->nullable()
                ->comment('出品者の最終既読日時')
                ->after('buyer_last_read_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropColumn('buyer_last_read_at');
            $table->dropColumn('seller_last_read_at');
        });
    }
};

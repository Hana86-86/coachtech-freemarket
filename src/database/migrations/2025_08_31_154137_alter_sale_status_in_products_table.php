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
        DB::statement("
            ALTER TABLE products
            MODIFY sale_status VARCHAR(20) NOT NULL DEFAULT '公開中'
        ");

        DB::table('products')->whereNull('sale_status')->update(['sale_status' => 'public']);
        DB::table('products')->whereIn('sale_status', ['公開中','公開停止中','取引中','public'])
            ->update(['sale_status' => 'public']);
        DB::table('products')->whereIn('sale_status', ['売却済み','sold'])
            ->update(['sale_status' => 'sold']);

        DB::statement("
            ALTER TABLE products
            MODIFY sale_status ENUM('public','sold') NOT NULL DEFAULT 'public'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("
            ALTER TABLE products
            MODIFY sale_status VARCHAR(20) NOT NULL DEFAULT 'public'
        ");

        DB::table('products')->where('sale_status','public')->update(['sale_status' => '公開中']);
        DB::table('products')->where('sale_status','sold')->update(['sale_status' => '売却済']);

        DB::statement("
            ALTER TABLE products
            MODIFY sale_status ENUM('公開中','売却済','取引中','公開停止中') NOT NULL DEFAULT '公開中'
        ");
    }
};

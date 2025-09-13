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
        DB::statement("ALTER TABLE products MODIFY sale_status VARCHAR(20) NOT NULL");

        DB::statement("UPDATE products SET sale_status = '公開中'   WHERE sale_status = 'public'");
        DB::statement("UPDATE products SET sale_status = '売却済み' WHERE sale_status = 'sold'");

        DB::statement("ALTER TABLE products
            MODIFY sale_status ENUM('公開中','売却済み') NOT NULL DEFAULT '公開中'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE products MODIFY sale_status VARCHAR(20) NOT NULL");
        DB::statement("UPDATE products SET sale_status = 'public' WHERE sale_status = '公開中'");
        DB::statement("UPDATE products SET sale_status = 'sold'   WHERE sale_status = '売却済み'");
        DB::statement("ALTER TABLE products
            MODIFY sale_status ENUM('public','sold') NOT NULL DEFAULT 'public'");
    }
};

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
        Schema::table('favorites', function (Blueprint $table) {

            $table->dropForeign(['product_id']);

            $table->dropUnique('favorites_product_id_visitor_token_unique');


            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('favorites', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unique('product_id', 'favorites_product_id_visitor_token_unique');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};

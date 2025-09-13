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
        if (Schema::hasTable('favorites')) {

        if (Schema::hasColumn('favorites', 'visitor_token')) {

            $exists = collect(DB::select(
                "SHOW INDEX FROM favorites WHERE Key_name = 'favorites_product_id_visitor_token_unique'"
            ))->isNotEmpty();

            Schema::table('favorites', function (Blueprint $table) use ($exists) {
                $table->dropForeign(['product_id']);

                if ($exists) {
                    $table->dropUnique('favorites_product_id_visitor_token_unique');
                }

                $table->foreign('product_id')
                        ->references('id')->on('products')
                        ->onDelete('cascade');
            });
        }
    }
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

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
        Schema::table('purchases', function (Blueprint $table) {
        if(!schema::hasColumn('purchases', 'payment_intent_id')) {

            $table->string('payment_intent_id')->nullable()->after('status');
            $table->unique('payment_intent_id');
        }
        $table->string('status', 20)->default('pending')->change();
        if (!$this->hasIndex('purchases', 'purchases_status_index')) {
            $table->index('status');
        }

        $table->string('payment_method', 20)->nullable()->change();

        });
        DB::table('purchases')->whereNotNull('paid_at')->update(['status' => 'completed']);
        DB::table('purchases')->whereNull('paid_at')->update(['status' => 'pending']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if ($this->hasIndex('purchases','purchase_status_index')) {
                $table->dropIndex(['status']);
            }
            if(Schema::hasColumn('purchases','payment_intent_id')) {
                $table->dropUnique(['payment_intent_id']);
                $table->dropColumn('payment_intent_id');
            }
            $table->string('status', 20)->default('paid')->change();
            $table->string('payment_method', 20)->nullable(false)->change();
        });
    }
    private function hasIndex(string $table, string $index):bool
    {
        $schema = DB::connection()->getDoctrineSchemaManager();
        $doctrineTable = $schema->introspectTable(DB::getTablePrefix() . $table);
        return $doctrineTable->hasIndex($index);
    }
};

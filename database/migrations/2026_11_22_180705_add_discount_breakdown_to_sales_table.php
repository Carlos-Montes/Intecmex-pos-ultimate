<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('customer_discount_amount', 12, 2)
                ->default(0)
                ->after('subtotal');

            $table->string('manual_discount_type', 20)
                ->default('amount')
                ->after('customer_discount_amount');

            $table->decimal('manual_discount_value', 12, 2)
                ->default(0)
                ->after('manual_discount_type');

            $table->decimal('manual_discount_amount', 12, 2)
                ->default(0)
                ->after('manual_discount_value');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'customer_discount_amount',
                'manual_discount_type',
                'manual_discount_value',
                'manual_discount_amount',
            ]);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('sale_id')
                ->constrained('sales')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            // Datos del producto al momento de la venta
            $table->string('product_code', 200)->nullable();
            $table->string('product_name', 200);

            // Precio por kilogramo
            $table->decimal('unit_price', 10, 2);

            // Peso vendido
            $table->decimal('quantity_kg', 10, 2);

            // Importe del producto
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_details');
    }
};

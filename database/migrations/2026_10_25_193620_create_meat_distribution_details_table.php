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
        Schema::create('meat_distribution_details', function (Blueprint $table) {
            $table->id();

            $table->foreignId('meat_distribution_id')
                ->constrained('meat_distributions')
                ->cascadeOnDelete();

            $table->foreignId('product_id')
                ->constrained('products')
                ->restrictOnDelete();

            $table->decimal('quantity_kg', 10, 2);

            $table->timestamps();

            $table->unique(
                ['meat_distribution_id', 'product_id'],
                'meat_distribution_product_unique'
            );
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meat_distribution_details');
    }
};

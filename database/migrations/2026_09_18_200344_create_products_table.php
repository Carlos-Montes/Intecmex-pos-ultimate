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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->integer('status')->nullable();
            $table->string('code')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->integer('category_id');
            $table->integer('subcategory_id');
            $table->string('file_path')->nullable();
            $table->string('image')->nullable();
            $table->decimal('price');
            $table->integer('in_discount')->nullable();
            $table->integer('discount')->nullable();
            $table->date('discount_until_date')->nullable();
            $table->text('content')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};

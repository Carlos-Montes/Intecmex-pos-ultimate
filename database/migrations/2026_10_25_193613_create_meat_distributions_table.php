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
        Schema::create('meat_distributions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('animal_merma_id')
                ->constrained('animal_mermas')
                ->restrictOnDelete();

            $table->string('user')->nullable();
            $table->timestamps();

            $table->index('animal_merma_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meat_distributions');
    }
};

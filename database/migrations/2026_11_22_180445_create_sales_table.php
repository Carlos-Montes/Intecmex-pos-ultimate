<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            $table->string('folio', 30)->unique();

            // Usuario que realiza la venta
            $table->foreignId('user_id')
                ->constrained('users')
                ->restrictOnDelete();

            // Cliente (opcional)
            $table->foreignId('customer_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Caja donde se realiza la venta
            $table->foreignId('cash_register_session_id')
                ->constrained('cash_register_sessions')
                ->restrictOnDelete();

            // Importes
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('tax_amount', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);

            // Pago en efectivo
            $table->string('payment_method', 20)->default('cash');
            $table->decimal('cash_received', 12, 2)->default(0);
            $table->decimal('change_amount', 12, 2)->default(0);

            // Estado de la venta
            $table->string('status', 20)->default('completed');

            $table->timestamp('sold_at')->useCurrent();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'sold_at']);
            $table->index(['cash_register_session_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};

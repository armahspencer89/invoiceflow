<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->foreignId('customer_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('invoice_number')->nullable();

            $table->unique(
                ['user_id', 'invoice_number'],
                'invoices_user_number_unique'
            );

            $table->date('invoice_date');
            $table->date('due_date');

            $table->timestamp('issued_at')->nullable();

            $table->string('status')->default('draft');

            $table->decimal('vat_rate', 5, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

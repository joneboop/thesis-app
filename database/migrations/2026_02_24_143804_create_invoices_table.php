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
    Schema::create('invoices', function (Blueprint $table) {
        $table->id();

        $table->foreignId('customer_id')
            ->constrained()
            ->cascadeOnDelete();

        $table->decimal('total_net', 12, 2);

        $table->enum('status', ['draft', 'issued', 'paid', 'cancelled'])
            ->default('draft');

        $table->timestamp('issued_at')->nullable();
        $table->timestamp('paid_at')->nullable();

        $table->timestamps();
    });
}
};

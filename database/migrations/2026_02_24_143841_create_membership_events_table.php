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
        Schema::create('membership_events', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->enum('type', [
                'evaluated',
                'upgraded',
                'downgraded',
                'downgrade_marked',
                'downgrade_cleared',
                'override_set',
                'override_cleared'
            ]);

            $table->foreignId('from_tier_id')
                ->nullable()
                ->constrained('membership_tiers')
                ->nullOnDelete();

            $table->foreignId('to_tier_id')
                ->nullable()
                ->constrained('membership_tiers')
                ->nullOnDelete();

            $table->decimal('window_spend', 12, 2)->nullable();

            $table->json('meta_json')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_events');
    }
};

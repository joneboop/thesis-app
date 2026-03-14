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
        Schema::create('customer_memberships', function (Blueprint $table) {
            $table->id();

            $table->foreignId('customer_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('current_tier_id')
                ->nullable()
                ->constrained('membership_tiers')
                ->nullOnDelete();

            $table->decimal('window_spend', 12, 2)->default(0);

            $table->timestamp('evaluated_at')->nullable();

            // Downgrade logic
            $table->foreignId('downgrade_candidate_tier_id')
                ->nullable()
                ->constrained('membership_tiers')
                ->nullOnDelete();

            $table->timestamp('downgrade_eligible_at')->nullable();

            // Manual override
            $table->foreignId('override_tier_id')
                ->nullable()
                ->constrained('membership_tiers')
                ->nullOnDelete();

            $table->timestamp('override_until')->nullable();
            $table->text('override_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_memberships');
    }
};

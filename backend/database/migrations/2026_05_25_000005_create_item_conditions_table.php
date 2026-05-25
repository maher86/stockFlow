<?php

declare(strict_types=1);

use App\Enums\ItemConditionValue;
use App\Enums\PriceTier;
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
        Schema::create('item_conditions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('condition')->default(ItemConditionValue::Normal->value);
            $table->string('price_tier')->default(PriceTier::N1->value);
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();

            $table->unique(['item_id', 'condition', 'price_tier']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_conditions');
    }
};

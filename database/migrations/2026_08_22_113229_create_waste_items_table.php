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
         Schema::create('waste_items', function (Blueprint $table) {
        $table->id();

        $table->foreignId('category_id')
            ->constrained('waste_categories')
            ->cascadeOnDelete();

        $table->foreignId('disposal_method_id')
            ->constrained('disposal_methods')
            ->restrictOnDelete();

        $table->string('name');
        $table->text('description')->nullable();
        $table->text('keywords')->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waste_items');
    }
};

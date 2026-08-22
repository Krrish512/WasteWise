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
        Schema::create('collection_centres', function (Blueprint $table) {
        $table->id();

        $table->foreignId('city_id')
            ->constrained('cities')
            ->cascadeOnDelete();

        $table->foreignId('category_id')
            ->constrained('waste_categories')
            ->restrictOnDelete();

        $table->string('name');
        $table->text('address');
        $table->string('phone')->nullable();

        $table->decimal('latitude', 10, 7)->nullable();
        $table->decimal('longitude', 10, 7)->nullable();

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_centres');
    }
};

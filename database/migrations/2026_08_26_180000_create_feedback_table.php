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
        Schema::create('feedback', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('name')->nullable();
            $table->string('email')->nullable();

            $table->unsignedTinyInteger('rating');
            $table->unsignedTinyInteger('ease_of_use');
            $table->boolean('helpful');
            $table->text('experience');
            $table->text('suggestion')->nullable();
            $table->boolean('recommend');

            $table->boolean('is_reviewed')->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};

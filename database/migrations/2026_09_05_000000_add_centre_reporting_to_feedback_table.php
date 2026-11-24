<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            if (!Schema::hasColumn('feedback', 'collection_centre_id')) {
                $table->foreignId('collection_centre_id')
                    ->nullable()
                    ->after('user_id')
                    ->constrained('collection_centres')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('feedback', 'issue_type')) {
                $table->string('issue_type')
                    ->nullable()
                    ->index()
                    ->after('collection_centre_id');
            }

            // Make standard feedback fields nullable so centre issue reports can be submitted cleanly
            if (DB::getDriverName() !== 'sqlite') {
                $table->unsignedTinyInteger('rating')->nullable()->change();
                $table->unsignedTinyInteger('ease_of_use')->nullable()->change();
                $table->boolean('helpful')->nullable()->change();
                $table->text('experience')->nullable()->change();
                $table->boolean('recommend')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feedback', function (Blueprint $table) {
            if (Schema::hasColumn('feedback', 'collection_centre_id')) {
                $table->dropForeign(['collection_centre_id']);
                $table->dropColumn('collection_centre_id');
            }
            if (Schema::hasColumn('feedback', 'issue_type')) {
                $table->dropColumn('issue_type');
            }
        });
    }
};

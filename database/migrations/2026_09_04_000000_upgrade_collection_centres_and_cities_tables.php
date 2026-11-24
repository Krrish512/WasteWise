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
        // 1. Add nullable indexed district to cities
        Schema::table('cities', function (Blueprint $table) {
            if (!Schema::hasColumn('cities', 'district')) {
                $table->string('district')->nullable()->index()->after('state');
            }
        });

        // 2. Add new columns and ensure nullable coordinates on collection_centres
        Schema::table('collection_centres', function (Blueprint $table) {
            if (!Schema::hasColumn('collection_centres', 'district')) {
                $table->string('district')->nullable()->index()->after('city_id');
            }

            // Ensure latitude and longitude are nullable decimal(10, 7)
            // (already nullable in base migration, ensure explicitly)
            if (DB::getDriverName() !== 'sqlite') {
                $table->decimal('latitude', 10, 7)->nullable()->change();
                $table->decimal('longitude', 10, 7)->nullable()->change();
            }

            if (!Schema::hasColumn('collection_centres', 'facility_type')) {
                $table->string('facility_type')->default('public_dropoff')->after('phone');
            }
            if (!Schema::hasColumn('collection_centres', 'public_access')) {
                $table->string('public_access')->default('public_dropoff')->after('facility_type');
            }
            if (!Schema::hasColumn('collection_centres', 'authorization_status')) {
                $table->string('authorization_status')->default('verified')->after('public_access');
            }
            if (!Schema::hasColumn('collection_centres', 'authorization_id')) {
                $table->string('authorization_id')->nullable()->after('authorization_status');
            }
            if (!Schema::hasColumn('collection_centres', 'authorization_valid_until')) {
                $table->date('authorization_valid_until')->nullable()->after('authorization_id');
            }
            if (!Schema::hasColumn('collection_centres', 'website')) {
                $table->string('website')->nullable()->after('authorization_valid_until');
            }
            if (!Schema::hasColumn('collection_centres', 'source_url')) {
                $table->string('source_url')->nullable()->after('website');
            }
            if (!Schema::hasColumn('collection_centres', 'verification_date')) {
                $table->date('verification_date')->nullable()->after('source_url');
            }
            if (!Schema::hasColumn('collection_centres', 'confidence')) {
                $table->string('confidence')->default('medium')->after('verification_date');
            }
            if (!Schema::hasColumn('collection_centres', 'status')) {
                $table->string('status')->default('active')->after('confidence');
            }
            if (!Schema::hasColumn('collection_centres', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
        });

        // 3. Create collection_centre_waste_category pivot table
        if (!Schema::hasTable('collection_centre_waste_category')) {
            Schema::create('collection_centre_waste_category', function (Blueprint $table) {
                $table->id();
                $table->foreignId('collection_centre_id')
                    ->constrained('collection_centres')
                    ->cascadeOnDelete();
                $table->foreignId('waste_category_id')
                    ->constrained('waste_categories')
                    ->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['collection_centre_id', 'waste_category_id'], 'cc_wc_unique');
            });
        }

        // 4. Backfill pivot table for existing records that have category_id
        if (Schema::hasTable('collection_centres') && Schema::hasColumn('collection_centres', 'category_id')) {
            $existing = DB::table('collection_centres')
                ->whereNotNull('category_id')
                ->get(['id', 'category_id']);

            foreach ($existing as $centre) {
                DB::table('collection_centre_waste_category')->insertOrIgnore([
                    'collection_centre_id' => $centre->id,
                    'waste_category_id'    => $centre->category_id,
                    'created_at'           => now(),
                    'updated_at'           => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('collection_centre_waste_category');

        Schema::table('collection_centres', function (Blueprint $table) {
            $columns = [
                'district',
                'facility_type',
                'public_access',
                'authorization_status',
                'authorization_id',
                'authorization_valid_until',
                'website',
                'source_url',
                'verification_date',
                'confidence',
                'status',
                'notes',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('collection_centres', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('cities', function (Blueprint $table) {
            if (Schema::hasColumn('cities', 'district')) {
                $table->dropColumn('district');
            }
        });
    }
};

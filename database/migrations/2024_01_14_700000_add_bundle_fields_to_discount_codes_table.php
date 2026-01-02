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
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->boolean('is_bundle')->default(0)->after('description');
            $table->decimal('bundle_price', 10, 2)->nullable()->after('is_bundle');
            $table->string('bundle_image')->nullable()->after('bundle_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('discount_codes', function (Blueprint $table) {
            $table->dropColumn(['is_bundle', 'bundle_price', 'bundle_image']);
        });
    }
};


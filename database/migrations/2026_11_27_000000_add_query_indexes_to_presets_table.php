<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('presets', function (Blueprint $table) {
            $table->index(['status'], 'presets_status_index');
            $table->index(['preset_price'], 'presets_price_index');
            $table->index(['status', 'preset_price'], 'presets_status_price_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['price'], 'products_price_index');
            $table->index(['preset_id', 'price'], 'products_preset_price_index');
        });
    }

    public function down(): void
    {
        Schema::table('presets', function (Blueprint $table) {
            $table->dropIndex('presets_status_index');
            $table->dropIndex('presets_price_index');
            $table->dropIndex('presets_status_price_index');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_price_index');
            $table->dropIndex('products_preset_price_index');
        });
    }
};

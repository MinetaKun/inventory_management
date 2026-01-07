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
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->foreignId('from_location_id')->nullable()->after('variant_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('to_location_id')->nullable()->after('from_location_id')->constrained('locations')->restrictOnDelete();
            $table->timestamp('due_date')->nullable()->after('status');
            
            // Make original location_id nullable as we might use from/to primarily
            $table->foreignId('location_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->dropForeign(['from_location_id']);
            $table->dropForeign(['to_location_id']);
            $table->dropColumn(['from_location_id', 'to_location_id', 'due_date']);
            
            // Revert location_id to not null (careful with data integrity here in real app)
            $table->foreignId('location_id')->nullable(false)->change();
        });
    }
};

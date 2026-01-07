<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('variant_id')->constrained('variants')->restrictOnDelete();
            $table->foreignId('location_id')->constrained('locations')->restrictOnDelete();
            $table->foreignId('shelf_id')->nullable()->constrained('shelves')->nullOnDelete();
            $table->foreignId('moved_by')->constrained('users')->restrictOnDelete();
            $table->enum('type', ['in', 'out', 'transfer', 'adjustment', 'return'])->default('transfer');
            $table->integer('quantity');
            $table->text('reason')->nullable();
            $table->text('notes')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled', 'archived'])->default('completed');
            $table->timestamps();

            $table->index('variant_id');
            $table->index('location_id');
            $table->index('shelf_id');
            $table->index('moved_by');
            $table->index('type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};

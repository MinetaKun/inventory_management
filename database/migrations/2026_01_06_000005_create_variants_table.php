<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->constrained('inventories')->cascadeOnDelete();
            $table->string('sku')->unique();
            $table->string('image')->nullable();
            $table->integer('quantity')->default(0);
            $table->text('note')->nullable();
            $table->enum('status', ['active', 'inactive', 'discontinued', 'archived'])->default('active');
            $table->timestamps();

            $table->index('inventory_id');
            $table->index('sku');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('variants');
    }
};

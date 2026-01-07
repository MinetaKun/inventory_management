<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->string('product_label')->unique();
            $table->string('sku_ref')->unique();
            $table->foreignId('category_id')->constrained('categories')->restrictOnDelete();
            $table->decimal('weight', 8, 2)->nullable();
            $table->decimal('cost_price', 10, 2)->nullable();
            $table->foreignId('added_by')->constrained('users')->restrictOnDelete();
            $table->enum('status', ['active', 'inactive', 'discontinued', 'archived'])->default('active');
            $table->timestamps();

            $table->index('product_label');
            $table->index('sku_ref');
            $table->index('category_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventories');
    }
};

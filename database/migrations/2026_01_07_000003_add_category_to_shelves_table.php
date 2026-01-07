<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shelves', function (Blueprint $table) {
            if (!Schema::hasColumn('shelves', 'category_id')) {
                $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete()->after('location_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('shelves', function (Blueprint $table) {
            if (Schema::hasColumn('shelves', 'category_id')) {
                $table->dropForeignKeyIfExists(['category_id']);
                $table->dropColumn('category_id');
            }
        });
    }
};

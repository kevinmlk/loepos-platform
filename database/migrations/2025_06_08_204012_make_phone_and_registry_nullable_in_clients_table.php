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
        Schema::table('clients', function (Blueprint $table) {
            // Drop existing unique constraints
            $table->dropUnique(['phone']);
            $table->dropUnique(['national_registry_number']);
        });
        
        Schema::table('clients', function (Blueprint $table) {
            // Make fields nullable
            $table->string('phone')->nullable()->change();
            $table->string('national_registry_number')->nullable()->change();
        });
        
        Schema::table('clients', function (Blueprint $table) {
            // Re-add unique constraints (nullable fields can have unique constraints)
            $table->unique('phone');
            $table->unique('national_registry_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            // Drop existing unique constraints
            $table->dropUnique(['phone']);
            $table->dropUnique(['national_registry_number']);
        });
        
        Schema::table('clients', function (Blueprint $table) {
            // Make fields non-nullable (this might fail if there are null values)
            $table->string('phone')->change();
            $table->string('national_registry_number')->change();
        });
        
        Schema::table('clients', function (Blueprint $table) {
            // Re-add unique constraints
            $table->unique('phone');
            $table->unique('national_registry_number');
        });
    }
};

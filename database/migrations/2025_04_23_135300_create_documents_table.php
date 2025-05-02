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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Dossier::class);
            $table->enum('type', ['factuur', 'herinnering', 'identiteit', 'overeenkomst']);
            $table->string('file_name');
            $table->string('file_path');
            $table->json('parsed_data')->nullable();
            $table->boolean('verified_status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};

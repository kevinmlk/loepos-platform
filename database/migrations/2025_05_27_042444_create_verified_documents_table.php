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
        Schema::create('verified_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('client_id')->constrained('clients');
            $table->foreignId('dossier_id')->constrained('dossiers');
            $table->foreignId('original_document_id')->nullable()->constrained('documents');
            $table->string('type');
            $table->string('file_name');
            $table->string('file_path');
            $table->string('sender')->nullable();
            $table->string('receiver')->nullable();
            $table->date('send_date')->nullable();
            $table->date('receive_date')->nullable();
            $table->date('due_date')->nullable();
            $table->json('verified_data')->nullable(); // Stores all verified form data
            $table->json('metadata')->nullable(); // Stores additional metadata
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['organization_id', 'client_id']);
            $table->index(['organization_id', 'dossier_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('verified_documents');
    }
};
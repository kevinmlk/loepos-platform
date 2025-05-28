<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Models\Document;
use App\Models\Dossier;
use App\Models\Upload;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Upload::class);
            $table->foreignIdFor(Dossier::class)->nullable();
            $table->enum('type', Document::TYPES);
            $table->string('file_name');
            $table->string('file_path');
            $table->json('parsed_data');
            $table->enum('status', Document::STATUS)->default(Document::STATUS_PENDING);
            $table->string('sender');
            $table->string('receiver');
            $table->decimal('amount')->nullable();
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

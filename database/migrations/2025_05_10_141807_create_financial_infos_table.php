<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Client;
use App\Models\FinancialInfo;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('financial_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Client::class);
            $table->string('iban');
            $table->string('bank_name');
            $table->decimal('monthly_income', 10, 2);
            $table->decimal('monthly_expenses', 10, 2);
            $table->string('employer');
            $table->enum('contract', FinancialInfo::CONTRACTS);
            $table->enum('education', FinancialInfo::EDUCATIONS);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_infos');
    }
};

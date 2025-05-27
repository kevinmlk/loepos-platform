<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Payment;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Payment::factory()->create([
            'debt_id' => 1,
            'document_id' => null,
            'amount' => 149.99,
            'method' => Payment::METHOD_TRANSFER,
        ]);

        Payment::factory()->create([
            'debt_id' => 1,
            'document_id' => null,
            'amount' => 350,
            'method' => Payment::METHOD_TRANSFER,
        ]);

        Payment::factory()->create([
            'debt_id' => 1,
            'document_id' => null,
            'amount' => 249.99,
            'method' => Payment::METHOD_TRANSFER,
        ]);

        Payment::factory()->create([
            'debt_id' => 2,
            'document_id' => null,
            'amount' => 39.98,
            'method' => Payment::METHOD_AUTOMATIC,
        ]);

        Payment::factory()->create([
            'debt_id' => 2,
            'document_id' => null,
            'amount' => 249.98,
            'method' => Payment::METHOD_AUTOMATIC,
        ]);

        Payment::factory()->create([
            'debt_id' => 2,
            'document_id' => null,
            'amount' => 223.25,
            'method' => Payment::METHOD_AUTOMATIC,
        ]);

        Payment::factory()->create([
            'debt_id' => 3,
            'document_id' => null,
            'amount' => 121.74,
            'method' => Payment::METHOD_TRANSFER,
        ]);

        Payment::factory()->create([
            'debt_id' => 3,
            'document_id' => null,
            'amount' => 101.55,
            'method' => Payment::METHOD_TRANSFER,
        ]);

        Payment::factory()->create([
            'debt_id' => 3,
            'document_id' => null,
            'amount' => 151.75,
            'method' => Payment::METHOD_TRANSFER,
        ]);

        Payment::factory()->create([
            'debt_id' => 4,
            'document_id' => null,
            'amount' => 250,
            'method' => Payment::METHOD_CASH,
        ]);

        Payment::factory()->create([
            'debt_id' => 4,
            'document_id' => null,
            'amount' => 250,
            'method' => Payment::METHOD_CASH,
        ]);

        Payment::factory()->create([
            'debt_id' => 4,
            'document_id' => null,
            'amount' => 250,
            'method' => Payment::METHOD_CASH,
        ]);

        Payment::factory()->create([
            'debt_id' => 5,
            'document_id' => null,
            'amount' => 88.55,
            'method' => Payment::METHOD_AUTOMATIC,
        ]);

        Payment::factory()->create([
            'debt_id' => 5,
            'document_id' => null,
            'amount' => 128.39,
            'method' => Payment::METHOD_AUTOMATIC,
        ]);

        Payment::factory()->create([
            'debt_id' => 5,
            'document_id' => null,
            'amount' => 112.66,
            'method' => Payment::METHOD_AUTOMATIC,
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('transaction_types')->insert([
            [
                'name' => 'Transfer',
                'code' => 'transfer',
                'action' => 'debit',
                'thumbnail' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Internet',
                'code' => 'internet',
                'action' => 'debit',
                'thumbnail' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Top Up',
                'code' => 'top_up',
                'action' => 'credit',
                'thumbnail' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Receiver',
                'code' => 'receiver',
                'action' => 'credit',
                'thumbnail' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

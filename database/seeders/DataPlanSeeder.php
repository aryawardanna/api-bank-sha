<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DataPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('data_plans')->insert([
            [
                'name' => '10 GB',
                'price' => 120000,
                'operator_card_id'=> 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '5 GB',
                'price' => 100000,
                'operator_card_id'=> 2,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '7 GB',
                'price' => 130000,
                'operator_card_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => '8 GB',
                'price' => 140000,
                'operator_card_id' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

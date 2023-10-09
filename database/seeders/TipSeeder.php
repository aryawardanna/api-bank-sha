<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('tips')->insert([
            [
                'title' => 'Tips 1',
                'thumbnail' => 'nabung.jpg',
                'url' => 'https://medium.com/@kodearya12/quick-speed-up-untuk-mempercepat-aplikasi-b762105f8c56',
                'description' => 'Tips 1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Tips 2',
                'thumbnail' => 'nabung.jpg',
                'url' => 'https://medium.com/@kodearya12/create-jwt-pada-laravel-10-e79acd39674f',
                'description' => 'Tips 2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Tips 3',
                'thumbnail' => 'nabung.jpg',
                'url' => 'https://medium.com/@kodearya12/create-jwt-pada-laravel-10-e79acd39674f',
                'description' => 'Tips 3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

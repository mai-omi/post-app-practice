<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run(): void
    {
        $tags = ['お知らせ', '技術メモ', '雑記'];

        foreach ($tags as $name) {
            Category::firstOrCreate(['name' => $name]);
        }
    }
}

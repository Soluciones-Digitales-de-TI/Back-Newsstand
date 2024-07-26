<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Category::factory(50)->create();
        foreach (config('database.constans.en.categories') as $category) {
            Category::factory()->create(
                [
                    'name' => $category,
                ]
            );
        }
    }
}

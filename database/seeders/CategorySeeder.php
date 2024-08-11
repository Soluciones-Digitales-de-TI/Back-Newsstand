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
        $images = ['cafe', 'hamburguesa', 'pizza', 'dona', 'pastel', 'galletas'];
        foreach (config('database.constans.es.categories') as $key => $category) {
            Category::factory()->create(
                [
                    'name' => $category,
                    'icon' => $images[$key],
                    'state' => 1,
                ]
            );
        }
        Category::factory(50)->create();
    }
}

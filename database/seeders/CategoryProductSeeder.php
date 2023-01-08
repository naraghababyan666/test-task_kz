<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::create([
            'id' => 1,
            'name' => 'Computer accessories',
            'parent_id' => null
        ]);
        Category::create([
            'id' => 2,
            'name' => 'Keyboards',
            'parent_id' => '1'
        ]);
        Category::create([
            'id' => 3,
            'name' => 'PC',
            'parent_id' => '1'
        ]);
        Category::create([
            'id' => 4,
            'name' => 'Notebook',
            'parent_id' => 3
        ]);
        Category::create([
            'id' => 5,
            'name' => 'Macbook',
            'parent_id' => 3
        ]);

        Product::create([
            'name' => 'MacBook Air 13 M2/8GB/256GB/RU 2022',
            'description' => 'Русская версия. Самый тонкий и легкий ноутбук Apple теперь стал суперсильным благодаря чипу Apple. Он быстро справляется с вашими задачами, задействуя потрясающую скорость 8-ядерного процессора.',
            'slug' => 'macbook_1',
            'price' => '90000',
            'category_id' => 5,
            'width' => 21,
            'height' => 30,
            'weight' => 2
        ]);
        Product::create([
            'name' => 'Lenovo IdeaPad 3 15IML05',
            'description' => 'Ноутбук Lenovo IdeaPad 3 15IML05 удобен в эксплуатации. Он отличается компактным размером - толщина корпуса равна 2 см.',
            'slug' => 'notebook_1',
            'price' => '41000',
            'category_id' => 4,
            'width' => 21,
            'height' => 30,
            'weight' => 2
        ]);
    }
}

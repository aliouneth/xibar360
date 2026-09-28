<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $categories = [
            [
                'name_fr' => 'POLITIQUE',
                'name_en' => 'POLITICS',
                'slug' => 'politique',
                'icon' => 'fa-landmark',
                'description' => 'Actualités politiques sénégalaises et internationales',
            ],
            [
                'name_fr' => 'ÉCONOMIE',
                'name_en' => 'ECONOMY',
                'slug' => 'economie',
                'icon' => 'fa-chart-line',
                'description' => 'Informations économiques et financières',
            ],
            [
                'name_fr' => 'SOCIÉTÉ',
                'name_en' => 'SOCIETY',
                'slug' => 'societe',
                'icon' => 'fa-users',
                'description' => 'Actualités sociétales et faits divers',
            ],
            [
                'name_fr' => 'SPORT',
                'name_en' => 'SPORTS',
                'slug' => 'sport',
                'icon' => 'fa-futbol',
                'description' => 'Actualités sportives locales et internationales',
            ],
            [
                'name_fr' => 'AFRIQUE',
                'name_en' => 'AFRICA',
                'slug' => 'afrique',
                'icon' => 'fa-globe-africa',
                'description' => 'Actualités africaines et régionales',
            ],
            [
                'name_fr' => 'MONDE',
                'name_en' => 'WORLD',
                'slug' => 'monde',
                'icon' => 'fa-globe',
                'description' => 'Actualités internationales et mondiales',
            ],
            [
                'name_fr' => 'PEOPLE',
                'name_en' => 'PEOPLE',
                'slug' => 'people',
                'icon' => 'fa-people-group',
                'description' => 'Actualités people et célébrités',
            ],
        ];

        foreach ($categories as $category) {
            // updateOrCreate keyed on the unique slug: a plain create() made a
            // second `db:seed` abort on categories_slug_unique, which also
            // stopped every later seeder in the chain from running.
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}

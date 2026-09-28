<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class ArticlesTableSeeder extends Seeder
{
    /**
     * Ensures the importer has an owner for imported articles (articles.user_id
     * is NOT NULL) and nothing else.
     *
     * This seeder used to create ten fabricated articles pointing at
     * https://example.com/articleN, five of them flagged is_featured, so the
     * homepage hero and every category block were filled with invented
     * headlines. Real content now comes exclusively from the RSS importer
     * (php artisan news:scan) or from articles created in the admin panel.
     */
    public function run(): void
    {
        if (! User::where('email', 'admin@example.com')->exists()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
        }
    }
}

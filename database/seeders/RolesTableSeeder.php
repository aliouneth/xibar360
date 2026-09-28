<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'slug' => 'admin', 'description' => 'Administrateur du système'],
            ['name' => 'Editor', 'slug' => 'editor', 'description' => 'Éditeur de contenu'],
            ['name' => 'Viewer', 'slug' => 'viewer', 'description' => 'Lecteur simple'],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(['slug' => $role['slug']], $role);
        }
    }
}

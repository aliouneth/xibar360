<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call(RolesTableSeeder::class);

        // No default credentials are seeded on purpose: a well-known
        // admin@example.com / password account is an open door into the
        // back office on any machine this seeder ever touches. Create the
        // first admin explicitly instead:
        //
        //   SEED_DEFAULT_ADMIN=true ADMIN_EMAIL=you@domain.tld \
        //     ADMIN_PASSWORD='<long-random-secret>' php artisan db:seed
        if (filter_var(env('SEED_DEFAULT_ADMIN', false), FILTER_VALIDATE_BOOLEAN)) {
            $email = env('ADMIN_EMAIL');
            $password = env('ADMIN_PASSWORD');

            if (! is_string($email) || $email === '' || ! is_string($password) || strlen($password) < 12) {
                $this->command->warn('SEED_DEFAULT_ADMIN is set but ADMIN_EMAIL / ADMIN_PASSWORD (min 12 chars) are missing; skipping admin creation.');
            } else {
                $admin = User::firstOrCreate(
                    ['email' => $email],
                    ['name' => 'Admin', 'password' => Hash::make($password)]
                );

                $admin->assignRole('admin');
            }
        }

        $this->call(CategoriesTableSeeder::class);
        $this->call(ArticlesTableSeeder::class);
        $this->call(AdsTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
    }
}

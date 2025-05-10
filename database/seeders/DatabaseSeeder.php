<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['id' => 1],
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('Welcome123!'), // Cambiar la contraseña
                'email_verified_at' => now(),]
        );

        $this->call([
            TemplateBackpackSeeder::class,
        ]);

    }
}

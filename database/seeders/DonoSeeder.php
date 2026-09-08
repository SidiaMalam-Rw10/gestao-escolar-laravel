<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DonoSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'miscool'],
            [
                'name' => 'MiScool',
                'role' => 'proprietario',
                'password' => Hash::make('miscool123'),
                'is_active' => true,
            ]
        );
    }
}
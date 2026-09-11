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
            ['username' => 'noskola'],
            [
                'name' => 'No Skola',
                'role' => 'proprietario',
                'password' => Hash::make('noskola123'),
                'is_active' => true,
            ]
        );
    }
}
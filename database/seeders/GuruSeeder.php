<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GuruSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User:: create([
            'name' => 'gurusekolah',
            'email' => 'guruwikrama@gmail.com',
            'password' => Hash::make('guru'),
            'role' => 'guru'
        ]);
    }
}

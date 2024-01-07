<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User:: create([
            'name' => 'staffsekolah',
            'email' => 'staffwikrama@gmail.com',
            'password' => Hash::make('staff'),
            'role' => 'staff'
        ]);
    }
}

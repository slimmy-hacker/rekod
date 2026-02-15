<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AdministrativeUnit;
use App\Models\Student;
use App\Models\User;
use App\Models\Program; // Added this import
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
       $unit = AdministrativeUnit::firstOrCreate(
        ['id' => 38],
        ['name' => 'BSc Computer Science'] // Or whatever the unit name should be
    );

        $count = 157; 
        $prefix = "C027-01-";
        $suffix = "/2022";

        for ($i = 1; $i <= $count; $i++) {
            // Generate sequential reg number: C027-01-0001/2022
            $regNo = $prefix . str_pad($i, 4, '0', STR_PAD_LEFT) . $suffix;

            // 2. Create the User for login
            $user = User::create([
                'name' => 'Student ' . $i,
                'email' => 'student' . $i . '@example.com',
                'password' => Hash::make('password'), 
                'role' => 'student', 
            ]);

            // 3. Create the Student Profile linked to the User and Program
            Student::create([
                'user_id' => $user->id,
                'reg_no' => $regNo,
                'year_of_study' => 'Year 3', 
                'program_id'   => $unit->id,
                'phone_number' => '07' . rand(10000000, 99999999),
            ]);
        }
    }
}
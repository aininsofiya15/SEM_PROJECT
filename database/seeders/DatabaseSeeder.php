<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\Lecturer;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password');

        

        // 1. Generate the Student Team Accounts
        $team = [
            [
                'matric_id' => 'CB23015', 
                'name' => 'Ainin Sofiya', 
                'email' => 'cb23015@student.umpsa.edu.my', 
                'program' => 'Faculty of Computing', 
                'password' => $defaultPassword
            ],
            [
                'matric_id' => 'CB23016', 
                'name' => 'Hidayah', 
                'email' => 'siti@student.umpsa.edu.my', 
                'program' => 'Faculty of Computing', 
                'password' => $defaultPassword
            ],
            [
                'matric_id' => 'CB23017', 
                'name' => 'Wahidah', 
                'email' => 'wahidah@student.umpsa.edu.my', 
                'program' => 'Faculty of Computing', 
                'password' => $defaultPassword
            ],
            [
                'matric_id' => 'CB23018', 
                'name' => 'Najihah', 
                'email' => 'najihah@student.umpsa.edu.my', 
                'program' => 'Faculty of Computing', 
                'password' => $defaultPassword
            ],
        ];

        foreach ($team as $member) {
            Student::updateOrCreate(
                ['matric_id' => $member['matric_id']], 
                $member
            );
        }

        // 2. Generate a Dummy Lecturer Account
        Lecturer::updateOrCreate(
            ['staff_id' => 'STF001'],
            [
                'name' => 'Dr. Fahim',
                'email' => 'lecturer@umpsa.edu.my',
                'research_group' => 'Software Engineering',
                'password' => $defaultPassword
            ]
        );
    }
}
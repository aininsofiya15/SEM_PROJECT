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
                'name' => 'Ainin', 
                'email' => 'ainin@student.umpsa.edu.my', 
                'program' => 'Bachelor of Computer Science (Software Engineering) with Honours', 
                'password' => $defaultPassword
            ],
            [
                'matric_id' => 'CB23016', 
                'name' => 'Hidayah', 
                'email' => 'siti@student.umpsa.edu.my', 
                'program' => 'Bachelor of Computer Science (Software Engineering) with Honours', 
                'password' => $defaultPassword
            ],
            [
                'matric_id' => 'CB23017', 
                'name' => 'Wahidah', 
                'email' => 'wahidah@student.umpsa.edu.my', 
                'program' => 'Bachelor of Computer Science (Software Engineering) with Honours', 
                'password' => $defaultPassword
            ],
            [
                'matric_id' => 'CB23018', 
                'name' => 'Najihah', 
                'email' => 'najihah@student.umpsa.edu.my', 
                'program' => 'Bachelor of Computer Science (Software Engineering) with Honours', 
                'password' => $defaultPassword
            ],
        ];

        foreach ($team as $member) {
            Student::updateOrCreate(
                ['matric_id' => $member['matric_id']], 
                $member
            );
        }

        // 2. Map 6 Student Names Per Program 
        $programData = [
            'Bachelor of Computer Science (Software Engineering) with Honours' => [
                'code' => 'CB', 'start' => 100,
                'names' => ['Ahmad Arif', 'Chong Wei', 'Divya Nair', 'Nur Alia', 'Tan Ming', 'Kavitha']
            ],
            'Bachelor of Computer Science (Computer Systems & Networking) with Honours' => [
                'code' => 'CA', 'start' => 200,
                'names' => ['Amir Rasydan', 'Lee Jian', 'Arun Kumar', 'Siti Hajar', 'Wong Siew', 'Rohan']
            ],
            'Bachelor of Computer Science (Computer Graphics & Multimedia) with Honours' => [
                'code' => 'CD', 'start' => 300,
                'names' => ['Khairul Anwar', 'Lim Mei', 'Shanti Devi', 'Farah Hanim', 'Ng Kok', 'Suresh']
            ],
            'Bachelor of Computer Science (Cybersecurity) with Honours' => [
                'code' => 'CF', 'start' => 400,
                'names' => ['Muaz Zulkifli', 'Teoh Jin', 'Haris Rao', 'Aisyah Sofea', 'Pua Kien', 'Meena']
            ],
        ];

        foreach ($programData as $programName => $info) {
            foreach ($info['names'] as $index => $name) {
                $count = $index + 1;
                $uniqueNumber = $info['start'] + $count;
                $matricId = $info['code'] . '23' . $uniqueNumber;
                
                Student::updateOrCreate(
                    ['matric_id' => $matricId],
                    [
                        'name' => $name,
                        'email' => strtolower($info['code']) . $uniqueNumber . '@student.umpsa.edu.my',
                        'program' => $programName,
                        'password' => $defaultPassword
                    ]
                );
            }
        }

        // 3. Generate a Dummy Lecturer Account
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
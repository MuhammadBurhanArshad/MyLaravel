<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $students = collect([
            1 => ['name' => 'Ahmed', 'email' => 'Ahmed@example.com'],
            2 => ['name' => 'Fatima', 'email' => 'Fatima@example.com'],
            3 => ['name' => 'Ali', 'email' => 'Ali@example.com'],
            4 => ['name' => 'Ayesha', 'email' => 'Ayesha@example.com'],
            5 => ['name' => 'Usman', 'email' => 'Usman@example.com']
        ]);

        $students->each(function ($student) {
            Student::insert($student);
        });

        // Inseting one record using create method
        // Student::create([
        //     'name' => 'John Doe',
        //     'email' => 'john@example.com'
        // ]);


        // Inserting record using fake data, we can also use this via loop
        Student::create([
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail()
        ]);
    }
}

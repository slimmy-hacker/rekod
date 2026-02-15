<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $number = str_pad(static::$sequence++, 4, '0', STR_PAD_LEFT);
        
        return [
            'reg_no' => "C027-01-{$number}/2022",
            'year_of_study' => $this->faker->randomElement(['Year 1', 'Year 2', 'Year 3', 'Year 4']),
            'phone_number' => $this->faker->phoneNumber(),
            // Automatically creates a User and assigns the ID
            'user_id' => User::factory(), 
            
            'program_id' => 38,
        ];
    }
}

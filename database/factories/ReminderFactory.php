<?php

namespace Database\Factories;

use App\Models\Reminder;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReminderFactory extends Factory
{
    protected $model = Reminder::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->jobTitle(),
            'start_date' => $this->faker->dateTime,
            'end_date' => $this->faker->dateTimeBetween('now', '+30 days')
        ];
    }
}

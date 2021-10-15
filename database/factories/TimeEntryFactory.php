<?php

namespace Database\Factories;

use App\Models\TimeEntry;
use Illuminate\Database\Eloquent\Factories\Factory;

class TimeEntryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TimeEntry::class;

    /**
     * Define the model's default state.
     *
     * @return array
     * 
     * 
     * 
     */
    public function definition()
    {
        $username = [
            'Amitav',
            'Jhon',
            'Michael',
            'James',
            'Robert',
            'William',
            'David',
            'Thomas',
            'Christopher',
            'Matthew',
            'Donald',
            'Andrew',
            'Edward',
            'Jason',
        ];

        $project = ['Youtube', 'Google Play', 'Vuejs', 'Twitter', 'MySQL Package', 'JSAlbum', 'Website'];
        return [
            'username' => $this->faker->randomElement($username),
            'project' =>$this->faker->randomElement($project),
            'date' => $this->faker->dateTimeBetween('-6 months', 'now'),
            'time' => rand(1, 8),
        ];
    }
}

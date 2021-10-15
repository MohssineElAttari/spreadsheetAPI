<?php

namespace Database\Seeders;

use App\Models\TimeEntry;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        TimeEntry::factory()->count(10)->create();
    }
}

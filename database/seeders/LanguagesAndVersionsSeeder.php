<?php

namespace Database\Seeders;

use App\Models\Language;
use App\Models\Version;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class LanguagesAndVersionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        Language::create(['name' => 'Java', 'code' => 'java', 'color' => '#b07219'])->versions()->saveMany([
            new Version(['name' => 'JDK 1.8.0_66', 'index' => 0]),
            new Version(['name' => 'JDK 9.0.1', 'index' => 1]),
            new Version(['name' => 'JDK 10.0.1', 'index' => 2]),
            new Version(['name' => 'JDK 11.0.4', 'index' => 3])
        ]);

        Language::create(['name' => 'C', 'code' => 'c', 'color' => '#555555'])->versions()->saveMany([
            new Version(['name' => 'GCC 5.3.0', 'index' => 0]),
            new Version(['name' => 'Zapcc 5.0.0', 'index' => 1]),
            new Version(['name' => 'GCC 7.2.0', 'index' => 2]),
            new Version(['name' => 'GCC 8.1.0', 'index' => 3]),
            new Version(['name' => 'GCC 9.1.0', 'index' => 4])
        ]);

        Language::create(['name' => 'PHP', 'code' => 'php', 'color' => '#4f5d95'])->versions()->saveMany([
            new Version(['name' => '5.6.16', 'index' => 0]),
            new Version(['name' => '7.1.11', 'index' => 1]),
            new Version(['name' => '7.2.5', 'index' => 2]),
            new Version(['name' => '7.3.10', 'index' => 3])
        ]);
    }
}

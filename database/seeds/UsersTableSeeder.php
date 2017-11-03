<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        foreach (range(0,15) as $user) {
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $fullname = $firstname . " " . $lastname;
            \App\User::create([
                'name' => $fullname,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'email' => $faker->unique()->safeEmail,
                'password' => bcrypt('password'),
                'remember_token' => str_random(10)
            ]);
        }
    }
}

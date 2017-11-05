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
        $genders = ['Homme','Femme','Autre'];
        foreach (range(0,15) as $user) {
            $firstname = $faker->firstName;
            $lastname = $faker->lastName;
            $fullname = $firstname . " " . $lastname;
            \App\User::create([
                'name' => $fullname,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'email' => $faker->unique()->safeEmail,
                'genre' => $faker->randomElement($genders),
                'country' => $faker->country,
                'city' => $faker->city,
                'birthdate' => \Carbon\Carbon::createFromFormat('Y-m-d',$faker->dateTimeThisCentury()->format('Y-m-d'))->format('Y-m-d'),
                'password' => bcrypt('password'),
                'remember_token' => str_random(10)
            ]);
        }
    }
}

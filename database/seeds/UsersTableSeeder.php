<?php

use Illuminate\Database\Seeder;
use Hash as Hash;

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
        $genders = ['male','female','other'];
        foreach (range(0,15) as $user) {
            \App\User::create([
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'username' => $faker->unique()->userName,
                'profile_image_id' => '1',
                'cover_image_id' => '2',
                'age' => $faker->numberBetween(1,100),
                'email' => $faker->unique()->safeEmail,
                'gender' => $faker->randomElement($genders),
                'country' => $faker->country,
                'city' => $faker->city,
                'phone' => $faker->phoneNumber,
                'birthdate' => \Carbon\Carbon::createFromFormat('Y-m-d',$faker->dateTimeThisCentury()->format('Y-m-d'))->format('Y-m-d'),
                'password' => 'artifex',
                'remember_token' => str_random(10)
            ]);
        }
    }
}

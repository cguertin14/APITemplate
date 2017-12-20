<?php

use Illuminate\Database\Seeder;

class ConversationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        foreach (range(0,9) as $item) {
            $conversation = \App\Conversation::query()->create([
                'name' => $faker->name,
                'muted' => $faker->numberBetween(0,1),
                'event_id' => \App\Event::all()->random()->id,
                'image_id' => \App\Image::all()->random()->id
            ]);

            foreach (range(0,25) as $item2) {
                $conversation->messages()->create([
                    'body' => $faker->paragraph(3),
                    'user_id' => \App\User::all()->random()->id,
                    'seen' => $item2 < 24 ? 0 : 1
                ]);
            }
            \App\User::all()->random()->conversations()->attach($conversation);
        }
    }
}

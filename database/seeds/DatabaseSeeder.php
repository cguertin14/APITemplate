<?php

use Illuminate\Database\Seeder;
use \Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        DB::table('users')->truncate();
        DB::table('images')->truncate();
        DB::table('events')->truncate();
        DB::table('friend_user')->truncate();
        DB::table('stats_users')->truncate();
        DB::table('stats_user_tokens')->truncate();
        DB::table('conversations')->truncate();

        $this->call(ImagesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(EventsTableSeeder::class);
        $this->call(ConversationsTableSeeder::class);
        $this->call(FriendsTableSeeder::class);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1');
    }
}

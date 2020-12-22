<?php

use Illuminate\Database\Seeder;

class statusNamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('status_names')->insert([
            'id' => 1,
            'name' => 'cart'
        ]);
    }
}

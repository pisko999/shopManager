<?php

use Illuminate\Database\Seeder;

class statusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('statuses')->insert([
            'id' => 1,
            'status_id' => 1
        ]);
    }
}

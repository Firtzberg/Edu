<?php

class UserTableSeeder extends Seeder {

    public function run() {
        DB::table('users')->delete();

        User::create(array('name' => 'Kristijan',
            'lozinka' => Hash::make('kristijan'),
            'role_id' => Role::select('id')->where('ime', '=', 'Administrator')->first()->id
            ));

        User::create(array('name' => 'Andrija',
            'lozinka' => Hash::make('andrija'),
            'role_id' => Role::select('id')->where('ime', '=', 'Asistent')->first()->id));

        User::create(array('name' => 'Hrvoje',
            'lozinka' => Hash::make('hrvoje'),
            'role_id' => Role::select('id')->where('ime', '=', 'Instruktor')->first()->id));
        
        User::create(array('name' => 'Antun',
            'lozinka' => Hash::make('antun'),
            'role_id' => Role::select('id')->where('ime', '=', 'Voditelj tečaja')->first()->id));
    }

}

<?php

class UserTableSeeder extends Seeder {

    public function run() {
        DB::table('users')->delete();

        User::create(array('name' => 'Admin',
            'lozinka' => Hash::make('admin'),
            'email' => 'ja@moje.ba',
            'role_id' => Role::select('id')->where('ime', '=', 'Administrator')->first()->id
            ));

        User::create(array('name' => 'Asistent',
            'lozinka' => Hash::make('asistent'),
            'email' => 'ja@moje.ba',
            'role_id' => Role::select('id')->where('ime', '=', 'Asistent')->first()->id));

        User::create(array('name' => 'Instruktor',
            'lozinka' => Hash::make('instruktor'),
            'email' => 'ja@moje.ba',
            'role_id' => Role::select('id')->where('ime', '=', 'Instruktor')->first()->id));
    }

}

<?php

class UserTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('users')->delete();

		User::create(array('name' => 'adi',
'lozinka' => Hash::make('adi'),
'email' => 'ja@moje.ba',
'is_admin' => true));
	}
}
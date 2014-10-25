<?php

class RolesTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('roles')->delete();

		$role = Role::create(
			array(
				'ime' => 'Instruktor',
				'opis' => 'Održava instrukcije.',
				)
			);
		$role->save();

		$role = Role::create(
			array(
				'ime' => 'Asistent',
				'opis' => 'Uklanja greške pri unosu prouzrođene od Instruktora.',
				)
			);
		$role->save();
	}
}
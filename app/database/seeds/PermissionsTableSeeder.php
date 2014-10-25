<?php

class PermissionsTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('permissions')->delete();

		$permission = Permission::create(
			array(
				'ime' => Permission::PERMISSION_ADD_UCIONICA,
				'opis' => 'Mogućnost dodavanja nove učionice',
				)
			);
		$permission->save();
	}
}
<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('MjereTableSeeder');
		$this->call('KategorijeTableSeeder');
		$this->call('PermissionsTableSeeder');
		$this->call('RolesTableSeeder');
		$this->call('PermissionRoleTableSeeder');
		$this->call('UserTableSeeder');
        $this->call('CjenovnikSeeder');
        $this->call('NeradniDaniTableSeeder');
	}

}

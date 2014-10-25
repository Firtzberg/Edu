<?php

class PermissionRoleTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('permission_role')->delete();

		$role = Role::where('ime', '=', 'Instruktor')->first();
		$permissionIds = Permission::select('id')->whereIn('ime', array(
			'ništa'
			//wanted permissions
			))->get()->lists('id');
		//$role->permissions()->attach($permissionIds);

		$role = Role::where('ime', '=', 'Asistent')->first();
		$permissionIds = Permission::select('id')->whereIn('ime', array(
				Permission::PERMISSION_ADD_UCIONICA
			))->get()->lists('id');
		$role->permissions()->attach($permissionIds);
	}
}
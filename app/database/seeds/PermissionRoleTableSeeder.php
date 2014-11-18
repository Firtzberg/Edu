<?php

class PermissionRoleTableSeeder extends Seeder {

    public function run() {
        DB::table('permission_role')->delete();

        $role = Role::where('ime', '=', 'Administrator')->first();
        $permissionIds = Permission::select('id')->whereIn('ime', array(
                    Permission::PERMISSION_DOWNLOAD_DATA,
                    Permission::PERMISSION_EDIT_STARTED_REZERVACIJA,
                    Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING,
                    Permission::PERMISSION_VIEW_KLIJENT,
                    Permission::PERMISSION_VIEW_PREDMET_KATEGORIJA,
                    Permission::PERMISSION_VIEW_ROLE,
                    Permission::PERMISSION_VIEW_UCIONICA,
                    Permission::PERMISSION_VIEW_USER,
                    Permission::PERMISSION_MANAGE_KLIJENT,
                    Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA,
                    Permission::PERMISSION_MANAGE_ROLE,
                    Permission::PERMISSION_MANAGE_UCIONICA,
                    Permission::PERMISSION_MANAGE_USER,
                    Permission::PERMISSION_REMOVE_NALATA,
                    Permission::PERMISSION_REMOVE_PREDMET_KATEGORIJA,
                    Permission::PERMISSION_REMOVE_ROLE,
                    Permission::PERMISSION_REMOVE_STARTED_REZERVACIJA,
                    Permission::PERMISSION_REMOVE_UCIONICA,
                    Permission::PERMISSION_REMOVE_USER,
                    Permission::PERMISSION_SEE_FOREIGN_IZVJESTAJ,
                    Permission::PERMISSION_SEE_GLOBAL_IZVJESTAJ,
                    Permission::PERMISSION_PASSWORD_RESET,
                ))->get()->lists('id');
        if (count($permissionIds) > 0)
            $role->permissions()->attach($permissionIds);

        $role = Role::where('ime', '=', 'Instruktor')->first();
        $permissionIds = Permission::select('id')->whereIn('ime', array(
                    Permission::PERMISSION_OWN_REZERVACIJA_HANDLING,
                    Permission::PERMISSION_VIEW_UCIONICA,
                ))->get()->lists('id');
        if (count($permissionIds) > 0)
            $role->permissions()->attach($permissionIds);

        $role = Role::where('ime', '=', 'Asistent')->first();
        $permissionIds = Permission::select('id')->whereIn('ime', array(
                    Permission::PERMISSION_EDIT_STARTED_REZERVACIJA,
                    Permission::PERMISSION_VIEW_KLIJENT,
                    Permission::PERMISSION_VIEW_PREDMET_KATEGORIJA,
                    Permission::PERMISSION_VIEW_UCIONICA,
                    Permission::PERMISSION_VIEW_USER,
                    Permission::PERMISSION_MANAGE_KLIJENT,
                    Permission::PERMISSION_REMOVE_NALATA,
                    Permission::PERMISSION_REMOVE_STARTED_REZERVACIJA,
                    Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING,
                ))->get()->lists('id');
        if (count($permissionIds) > 0)
            $role->permissions()->attach($permissionIds);
    }

}

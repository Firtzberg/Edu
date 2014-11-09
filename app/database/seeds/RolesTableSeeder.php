<?php

class RolesTableSeeder extends Seeder {

    public function run() {
        DB::table('roles')->delete();

        Role::create(
                array(
                    'ime' => 'Administrator',
                    'opis' => 'Ima sve ovlasti u sustavu i određuje ovlasti drugima.',
                )
        );

        Role::create(
                array(
                    'ime' => 'Instruktor',
                    'opis' => 'Održava instrukcije.',
                )
        );

        Role::create(
                array(
                    'ime' => 'Asistent',
                    'opis' => 'Uklanja greške pri unosu prouzročene od Instruktora.',
                )
        );
    }

}

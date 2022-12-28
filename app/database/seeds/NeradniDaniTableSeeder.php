<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of NeradniDaniTableSeeder
 *
 * @author Hrvoje
 */
class NeradniDaniTableSeeder extends Seeder {

    public function run() {

        NeradniDan::create(
            array(
                'naziv' => 'Nova Godina',
                'dan' => 1,
                'mjesec' => 1,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Bogojavljenje',
                'dan' => 6,
                'mjesec' => 1,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Praznik rada',
                'dan' => 1,
                'mjesec' => 5,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Dan državnosti',
                'dan' => 30,
                'mjesec' => 5,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Dan antifašističke borbe',
                'dan' => 22,
                'mjesec' => 6,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Dan domovinske zahvalnosti',
                'dan' => 5,
                'mjesec' => 8,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Velika Gospa',
                'dan' => 15,
                'mjesec' => 8,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Dan Svih Svetih',
                'dan' => 1,
                'mjesec' => 11,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Dan sjećanja na žrtve Domovinskog rata',
                'dan' => 18,
                'mjesec' => 11,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Božić',
                'dan' => 25,
                'mjesec' => 12,
                'godina' => null,
            )
        )->save();

        NeradniDan::create(
            array(
                'naziv' => 'Sveti Stjepan',
                'dan' => 26,
                'mjesec' => 12,
                'godina' => null,
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_VIEW_NERADNI_DAN,
                'opis' => 'Dozvola za pregledavanje popisa neradnih dana. Ne uključuje dodavanje novih neradnih dana, niti uređivanje ni brisanje postojećih.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_MANAGE_NERADNI_DAN,
                'opis' => 'Dozvola za dodavanje novih neradnih dana te uređivanje i brisanje postojećih. Zahtjeva ' . Permission::PERMISSION_VIEW_NERADNI_DAN . '.',
                )
        )->save();
    }

}

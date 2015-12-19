<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of CjenovnikSeeder
 *
 * @author Hrvoje
 */
class CjenovnikSeeder extends Seeder {

    public function run() {

        Permission::create(
                array(
                    'ime' => Permission::PERMISSION_VIEW_CJENOVNIK,
                    'opis' => 'Dozvola za pregledavanje popisa cjenovnika i svake pojedinog cjenovnika. Ne uključuje dodavanje novih cjenovnika, niti uređivanje ni brisanje postojećih.',
                )
        )->save();

        Permission::create(
                array(
                    'ime' => Permission::PERMISSION_MANAGE_CJENOVNIK,
                    'opis' => 'Dozvola za dodavanje novih cjenovnika i uređivanje postojećih. Uklonjanje cjenovnika nije uključeno u ovu dozvolu. Zahtjeva ' . Permission::PERMISSION_VIEW_CJENOVNIK . '.',
                )
        )->save();

        Permission::create(
                array(
                    'ime' => Permission::PERMISSION_REMOVE_CJENOVNIK,
                    'opis' => 'Dozvola za uklonjanje cjenovnika. Zahtjeva ' . Permission::PERMISSION_MANAGE_CJENOVNIK . '.',
                )
        )->save();
    }

}

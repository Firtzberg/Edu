<?php

class PermissionsTableSeeder extends Seeder {

    public function run() {
        DB::table('permissions')->delete();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_VIEW_UCIONICA,
                'opis' => 'Dozvola za pregledavanje popisa učionica i svake pojedine učionice. Ne uključuje dodavanje novih učionica, niti uređivanje ni brisanje postojećih.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_VIEW_KLIJENT,
                'opis' => 'Dozvola za pregledavanje popisa klijenata i svakog pojedinog klijenta. Ne uključuje dodavanje novih klijenata, niti uređivanje ni brisanje postojećih.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_VIEW_PREDMET_KATEGORIJA,
                'opis' => 'Dozvola za pregledavanje strukture kategorija i predmeta i svakog pojedinog predmeta. Ne uključuje dodavanje novih predmeta ili kategorija, niti uređivanje ni brisanje postojećih.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_VIEW_ROLE,
                'opis' => 'Dozvola za pregledavanje popisa uloga i svake pojedine uloge. Ne uključuje dodavanje novih uloga, niti uređivanje ni brisanje postojećih.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_VIEW_USER,
                'opis' => 'Dozvola za pregledavanje popisa djelatnika i svakog pojedinog djelatnika. Ne uključuje dodavanje novih djelatnika, niti uređivanje ni brisanje postojećih.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_MANAGE_UCIONICA,
                'opis' => 'Dozvola za dodavanje novih učionica i uređivanje postojećih. Uklonjanje učionica nije uključeno u ovu dozvolu. Zahtjeva '.Permission::PERMISSION_VIEW_UCIONICA.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_MANAGE_KLIJENT,
                'opis' => 'Dozvola za pregledanje, dodavanje novih i uređivanje postojećih klijenata. Uklonjanje klijenata nije uključeno u ovu dozvolu. Zahtjeva '.Permission::PERMISSION_VIEW_KLIJENT.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA,
                'opis' => 'Dozvola za pregledanje, dodavanje novih i uređivanje postojećih kategorija i predmeta. Uključuje dodjeljivanje/oduzimanje predmeta pojedinom djelatniku. Uklonjanje kategorija i predmeta nije uključeno u ovu dozvolu. Zahtjeva '.Permission::PERMISSION_VIEW_PREDMET_KATEGORIJA.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_MANAGE_ROLE,
                'opis' => 'Dozvola za pregledanje, dodavanje novih i uređivanje postojećih uloga. Uključuje određivanje dozvola pojedine uloge (tako i svojoj vlastitoj ulozi). Uklonjanje uloga nije uključeno u ovu dozvolu. Zahtjeva '.Permission::PERMISSION_VIEW_ROLE.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_MANAGE_USER,
                'opis' => 'Dozvola za dodavanje novih djelatnika i uređivanje postojećih. Također uključuje dozvolu za promjenom uloge bilo kojem djelatniku (pa i sebi samome). Uklonjanje djelatnika nije uključeno u ovu dozvolu. Zahtjeva '.Permission::PERMISSION_VIEW_USER.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_REMOVE_UCIONICA,
                'opis' => 'Dozvola za uklonjanje učionica. Zahtjeva '.Permission::PERMISSION_MANAGE_UCIONICA.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_REMOVE_PREDMET_KATEGORIJA,
                'opis' => 'Dozvola za uklonjanje predmeta i čitavih kategorija. Zahtjeva '.Permission::PERMISSION_MANAGE_PREDMET_KATEGORIJA.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_REMOVE_ROLE,
                'opis' => 'Dozvola za uklonjanje uloga. Zahtjeva '.Permission::PERMISSION_MANAGE_ROLE.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_REMOVE_USER,
                'opis' => 'Dozvola za uklonjanje djelatnika.  Zahtjeva '.Permission::PERMISSION_MANAGE_USER.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_PASSWORD_RESET,
                'opis' => 'Dozvola za promjenom lozinke drugom djelatniku.  Zahtjeva '.Permission::PERMISSION_MANAGE_USER.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_OWN_REZERVACIJA_HANDLING,
                'opis' => 'Dozvola za rezerviranje u vlastito ime i uređivanje postojeće vlastite rezervacije, prije njenog početka. Dozvola za dodavanje novog klijenta ukoliko se ne pojavljuje u sustavu.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_MANAGE_NAPLATA,
                'opis' => 'Dozvola za naplaćivanje vlastite rezervacije nakon odrade.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_TECAJ,
                'opis' => 'Dodaje mogućnost da se rezervacije ovog instruktora označe kao tečajevi. Zahtjeva '.Permission::PERMISSION_OWN_REZERVACIJA_HANDLING.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_FOREIGN_REZERVACIJA_HANDLING,
                'opis' => 'Dozvola za rezerviranje u ime druge osobe i uređivanje postojeće tuđe rezervacije, prije njenog početka. Dozvola za dodavanje novog klijenta ukoliko se ne pojavljuje u sustavu.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_EDIT_STARTED_REZERVACIJA,
                'opis' => 'Dozvola za uređivanje rezervacije, nakon njenog početka, bilo osobne ili tuđe. Dozvola za dodavanje novog klijenta ukoliko se ne pojavljuje u sustavu.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_REMOVE_STARTED_REZERVACIJA,
                'opis' => 'Dozvola za uklonjanje započete rezervacije sa njenom naplatom, bilo osobne ili tuđe.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_REMOVE_NALATA,
                'opis' => 'Dozvola za uklonjanje naplate, bilo osobne ili tuđe.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_SEE_GLOBAL_IZVJESTAJ,
                'opis' => 'Dozvola za gledanje ukupnog izvještaja, bilo tjednog ili mjesečnog.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_SEE_FOREIGN_IZVJESTAJ,
                'opis' => 'Dozvola za gledanje izvještaja drugog djelatnika, bilo tjednog ili mjesečnog. Zahtjeva '.Permission::PERMISSION_MANAGE_USER.'.',
            )
        )->save();

        Permission::create(
            array(
                'ime' => Permission::PERMISSION_DOWNLOAD_DATA,
                'opis' => 'Dozvola za preuzimanje pohranjenih podataka u sustavu u obliku Excel tablica.',
            )
        )->save();
    }

}

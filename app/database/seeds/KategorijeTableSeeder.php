<?php

class KategorijeTableSeeder extends Seeder {

    public function run() {
        DB::table('kategorije')->delete();

        Kategorija::create(
                array(
                    'id' => 1,
                    'ime' => 'Home',
                    'nadkategorija_id' => 1,
                )
        );
    }

}

<?php

class MjereTableSeeder extends Seeder {

    public function run() {
        DB::table('mjere')->delete();

        Mjera::create(
                array(
                    'simbol' => 'PS',
                    'znacenje' => 'Puni sat',
                    'trajanje' => 60
                )
        );
        Mjera::create(
                array(
                    'simbol' => 'ŠS',
                    'znacenje' => 'Školski sat',
                    'trajanje' => 45
                )
        );
    }

}

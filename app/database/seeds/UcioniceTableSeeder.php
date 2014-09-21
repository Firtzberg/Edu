<?php

class UcioniceTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('ucionice')->delete();

		$ucionica = Ucionica::create(
			array(
				'naziv' => 'Mala',
				'max_broj_ucenika' => 6,
				'adresa' => 'Vukovarska 10, 31000 Osijek',
				'kat' => 1,
				'opis' => 'Mala ucionica s bijom tablom'
				)
			);
		$ucionica->save();
		$ucionica = Ucionica::create(
			array(
				'naziv' => 'Srednja',
				'max_broj_ucenika' => 12,
				'adresa' => 'Vukovarska 10, 31000 Osijek',
				'kat' => 1,
				'opis' => 'Srednja ucionica s bijom tablom'
				)
			);
		$ucionica->save();
		$ucionica = Ucionica::create(
			array(
				'naziv' => 'Velika',
				'max_broj_ucenika' => 24,
				'adresa' => 'Vukovarska 10, 31000 Osijek',
				'kat' => 1,
				'opis' => 'Velika ucionica s bijom tablom'
				)
			);
		$ucionica->save();
	}
}
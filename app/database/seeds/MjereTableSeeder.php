<?php

class MjereTableSeeder extends Seeder {
	
	public function run()
	{
		DB::table('mjere')->delete();

		$mjera = mjera::create(
			array(
				'simbol' => 'PS',
				'znacenje' => 'Puni sat',
				'trajanje' => 60
				)
			);
		$mjera->save();
		$mjera = mjera::create(
			array(
				'simbol' => 'ŠS',
				'znacenje' => 'Školski sat',
				'trajanje' => 45
				)
			);
		$mjera->save();
	}
}
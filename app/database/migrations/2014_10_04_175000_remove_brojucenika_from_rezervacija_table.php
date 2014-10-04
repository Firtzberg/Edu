<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveBrojucenikaFromRezervacijaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->dropColumn('broj_ucenika');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->integer('broj_ucenika')
			->unsigned();
		});
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKolicinaAndMjeraToRezervacijeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->integer('mjera_id')
			->after('pocetak_rada')
			->unsigned();
			$table->integer('kolicina')
			->after('mjera_id')
			->unsigned();
			$table->dropColumn('kraj_rada');
		});
		Schema::table('rezervacije', function(Blueprint $table){
			$table->foreign('mjera_id')
			->references('id')->on('mjere')
			->onUpdate('cascade');
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
			$table->dropForeign('rezervacije_mjera_id_foreign');

			$table->dateTime('kraj_rada');
			$table->dropColumn(array('mjera_id', 'kolicina'));
		});
	}

}

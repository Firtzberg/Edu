<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConfigRezervacijaTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('naplate', function(Blueprint $table)
		{
			$table->integer('stvarna_kolicina')
			->unsigned();
			$table->integer('stvarna_mjera')
			->unsigned()
			->index();
			$table->foreign('stvarna_mjera')
			->references('id')->on('mjere')
			->onUpdate('cascade');
		});

		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->integer('predmet_id')
			->unsigned()
			->nullable()
			->index();
			$table->foreign('predmet_id')
			->references('id')->on('predmeti')
			->onDelete('set null')
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
		Schema::table('naplate', function(Blueprint $table)
		{
			$table->dropForeign('naplate_stvarna_mjera_foreign');
			$table->dropColumn('stvarna_mjera');
			$table->dropColumn('stvarna_kolicina');
		});

		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->dropForeign('rezervacije_predmet_id_foreign');
			$table->dropColumn('predmet_id');
		});
	}

}

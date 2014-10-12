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
			$table->dropColumn('id');
			$table->primary('rezervacija_id');
			$table->foreign('stvarna_mjera')
			->references('id')->on('mjere')
			->onUpdate('cascade');
		});

		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->dropColumn('usmjerenje');
			$table->dropColumn('predmet');
			$table->integer('predmet_id')
			->unsigned()
			->nullable()
			->index();
			$table->foreign('predmet_id')
			->references('id')->on('predmeti')
			->onDelete('set null')
			->onUpdate('cascade');
		});

		Schema::table('klijent_rezervacija', function(Blueprint $table)
		{
			$table->boolean('missed');
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
			$table->dropPrimary();
			$table->dropForeign('naplate_stvarna_mjera_foreign');
			$table->dropColumn('stvarna_mjera');
			$table->dropColumn('stvarna_kolicina');
		});
		
		Schema::table('naplate', function(Blueprint $table)
		{
			$table->increments('id');
		});

		Schema::table('klijent_rezervacija', function(Blueprint $table)
		{
			$table->dropColumn('missed');
		});

		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->dropForeign('rezervacije_predmet_id_foreign');
			$table->dropColumn('predmet_id');
			$table->string('usmjerenje');
			$table->string('predmet');
		});
	}

}

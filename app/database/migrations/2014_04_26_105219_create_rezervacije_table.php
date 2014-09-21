<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRezervacijeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('rezervacije', function(Blueprint $table)
		{
			$table->increments('id');
			$table->dateTime('pocetak_rada');
			$table->dateTime('kraj_rada');
			$table->integer('broj_ucenika');
			$table->integer('instruktor_id')
			->unsigned();
			$table->integer('ucionica_id')
			->unsigned()
			->nullable();
			$table->string('usmjerenje')
			->nullable();
			$table->string('predmet')
			->nullable();
			$table->timestamps();
		});

		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->foreign('ucionica_id')
			->references('id')->on('ucionice')
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
		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->dropForeign('rezervacije_ucionica_id_foreign');
		});

		Schema::drop('rezervacije');
	}

}

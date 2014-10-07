<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUcioniceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('ucionice', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('naziv')
			->unique();
			$table->integer('max_broj_ucenika')
			->unsigned();
			$table->string('adresa');
			$table->integer('kat');
			$table->text('opis');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('ucionice');
	}

}

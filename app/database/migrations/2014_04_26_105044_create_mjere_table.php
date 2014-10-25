<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMjereTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('mjere', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('simbol', 5);
			$table->string('znacenje');
			$table->integer('trajanje')
			->unsigned();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('mjere');
	}

}

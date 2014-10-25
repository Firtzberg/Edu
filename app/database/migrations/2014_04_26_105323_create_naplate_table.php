<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNaplateTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('naplate', function(Blueprint $table)
		{
			$table->integer('ukupno_uplaceno');
			$table->integer('za_instruktora');
			$table->integer('za_tvrtku');
			$table->integer('rezervacija_id')
			->unsigned()
			->index();
			$table->timestamps();
		});

		Schema::table('naplate', function(Blueprint $table)
		{
			$table->primary('rezervacija_id');
			$table->foreign('rezervacija_id')
			->references('id')->on('rezervacije')
			->onDelete('cascade')
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
			$table->dropForeign('naplate_rezervacija_id_foreign');
		});

		Schema::drop('naplate');
	}

}

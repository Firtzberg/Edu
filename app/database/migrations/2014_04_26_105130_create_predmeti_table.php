<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePredmetiTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('predmeti', function(Blueprint $table){
			$table->increments('id');
			$table->string('ime');
			$table->integer('kategorija_id')
			->unsigned();
			$table->timestamps();
		});
		Schema::table('predmeti', function(Blueprint $table)
		{
			$table->foreign('kategorija_id')
			->references('id')->on('kategorije')
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
		Schema::table('predmeti', function(Blueprint $table){
			$table->dropForeign('predmeti_kategorija_id_foreign');
		});
		Schema::drop('predmeti');
	}

}

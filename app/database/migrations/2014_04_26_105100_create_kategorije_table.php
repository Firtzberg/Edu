<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKategorijeTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('kategorije', function(Blueprint $table){
			$table->increments('id');
			$table->string('ime');
			$table->integer('nadkategorija_id')
			->unsigned();
			$table->timestamps();
		});
		Schema::table('kategorije', function(Blueprint $table){
			$table->foreign('nadkategorija_id')
			->references('id')->on('kategorije')
			->onUpdate('cascade')
			->onDelete('cascade');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('kategorije', function(Blueprint $table){
			$table->dropForeign('kategorije_nadkategorija_id_foreign');
		});
		Schema::drop('kategorije');
	}

}

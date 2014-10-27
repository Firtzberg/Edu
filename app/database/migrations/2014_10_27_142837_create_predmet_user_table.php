<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePredmetUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('predmet_user', function(Blueprint $table)
		{
			$table->integer('user_id')
			->unsigned()
			->index();
			$table->integer('predmet_id')
			->unsigned()
			->index();
		});

		Schema::table('predmet_user', function(Blueprint $table)
		{
			$table->foreign('user_id')
			->references('id')->on('users')
			->onDelete('cascade')
			->onUpdate('cascade');
			$table->foreign('predmet_id')
			->references('id')->on('predmeti')
			->onDelete('cascade')
			->onUpdate('cascade');
			$table->primary(array('user_id', 'predmet_id'));
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('predmet_user', function(Blueprint $table)
		{
			$table->dropForeign('predmet_user_user_id_foreign');
			$table->dropForeign('predmet_user_predmet_id_foreign');
		});
		Schema::drop('predmet_user');
	}

}

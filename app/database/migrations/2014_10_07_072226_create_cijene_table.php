<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCijeneTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('cijene', function(Blueprint $table)
		{
			$table->integer('predmet_id')
			->unsigned()
			->index();
			$table->integer('mjera_id')
			->unsigned()
			->index();
			$table->integer('individualno')
			->unsigned();
			$table->integer('popust')
			->unsigned();
			$table->integer('minimalno')
			->unsigned();
			$table->timestamps();
		});
		Schema::table('cijene', function(Blueprint $table)
		{
			$table->foreign('predmet_id')
			->references('id')->on('predmeti')
			->onUpdate('cascade')
			->onDelete('cascade');
			$table->foreign('mjera_id')
			->references('id')->on('mjere')
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
		Schema::table('cijene', function(Blueprint $table){
			$table->dropForeign('cijene_mjera_id_foreign');
			$table->dropForeign('cijene_predmet_id_foreign');
		});
		Schema::drop('cijene');
	}

}

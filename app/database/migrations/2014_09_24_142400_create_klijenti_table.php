<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKlijentiTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('klijenti', function(Blueprint $table){
			$table->char('broj_mobitela', 14);
			$table->string('ime');
			$table->string('email')
			->nullable();
			$table->string('facebook')
			->nullable();
			$table->timestamps();
			$table->primary('broj_mobitela');
		});

		Schema::create('klijent_rezervacija', function(Blueprint $table){
			$table->char('klijent_id', 14)
			->nullable()
			->index();
			$table->integer('rezervacija_id')
			->unsigned()
			->index();
		});

		Schema::table('klijent_rezervacija', function(Blueprint $table){
			$table->foreign('klijent_id')
			->references('broj_mobitela')->on('klijenti')
			->onUpdate('cascade')
			->onDelete('set null');
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
		Schema::table('klijent_rezervacija', function(Blueprint $table)
		{
			$table->dropForeign('klijent_rezervacija_klijent_id_foreign');
			$table->dropForeign('klijent_rezervacija_rezervacija_id_foreign');
		});
		Schema::drop('klijent_rezervacija');
		Schema::drop('klijenti');
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('users', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('name')
			->unique();
			$table->string('remember_token');
			$table->boolean('is_admin')
			->default(false);
			$table->string('broj_mobitela');
			$table->string('email');
			$table->string('lozinka');
			$table->timestamps();
		});

		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->foreign('instruktor_id')
			->references('id')->on('users')
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
		Schema::table('rezervacije', function(Blueprint $table)
		{
			$table->dropForeign('rezervacije_instruktor_id_foreign');
		});
		
		Schema::drop('users');
	}

}

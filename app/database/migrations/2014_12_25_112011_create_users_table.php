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
			$table->char('boja', 6)
			->default('ffffff');
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
		Schema::drop('users');
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddKlijentFields extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('klijenti', function(Blueprint $table)
		{
			$table->string('roditelj')
                                ->nullable();
                        $table->char('broj_roditelja', 20)
                                ->nullable();
			$table->string('skola', 50)
                                ->nullable();
			$table->tinyInteger('razred')
                                ->nullable()
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
		Schema::table('klijenti', function(Blueprint $table)
		{
			$table->dropColumn('roditelj');
			$table->dropColumn('broj_roditelja');
			$table->dropColumn('skola');
			$table->dropColumn('razred');
		});
	}

}

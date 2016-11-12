<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPolozajToUcioniceTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('ucionice', function(Blueprint $table)
		{
			$table->integer('polozaj')
				->default(0);
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('ucionice', function(Blueprint $table)
		{
			$table->dropColumn('polozaj');
		});
	}

}

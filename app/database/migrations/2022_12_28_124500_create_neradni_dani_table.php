<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNeradniDaniTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::create('neradni_dani', function(Blueprint $table) {
            $table->increments('id');
            $table->string('naziv', 50);
            $table->integer('dan')
                ->unsigned();
            $table->integer('mjesec')
                ->unsigned();
            $table->integer('godina')
                ->unsigned()
                ->nullable();
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
        Schema::drop('neradni_dani');
	}

}

<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRezervacijeTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('rezervacije', function(Blueprint $table) {
            $table->increments('id');
            $table->dateTime('pocetak_rada');
            $table->integer('mjera_id')
                    ->unsigned()
                    ->index();
            $table->integer('kolicina')
                    ->unsigned();
            $table->integer('instruktor_id')
                    ->unsigned()
                    ->index();
            $table->integer('predmet_id')
                    ->unsigned()
                    ->nullable()
                    ->index();
            $table->integer('ucionica_id')
                    ->unsigned()
                    ->nullable()
                    ->index();
            $table->boolean('tecaj')
                    ->default(false);
            $table->string('napomena')
                    ->nullable();
            $table->timestamps();
        });

        Schema::table('rezervacije', function(Blueprint $table) {
            $table->foreign('mjera_id')
                    ->references('id')->on('mjere')
                    ->onUpdate('cascade');

            $table->foreign('instruktor_id')
                    ->references('id')->on('users')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

            $table->foreign('predmet_id')
                    ->references('id')->on('predmeti')
                    ->onDelete('set null')
                    ->onUpdate('cascade');

            $table->foreign('ucionica_id')
                    ->references('id')->on('ucionice')
                    ->onDelete('set null')
                    ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('rezervacije', function(Blueprint $table) {
            $table->dropForeign('rezervacije_mjera_id_foreign');
            $table->dropForeign('rezervacije_predmet_id_foreign');
            $table->dropForeign('rezervacije_ucionica_id_foreign');
            $table->dropForeign('rezervacije_instruktor_id_foreign');
        });

        Schema::drop('rezervacije');
    }

}

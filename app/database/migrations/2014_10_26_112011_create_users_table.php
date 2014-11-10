<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');
            $table->string('name')
                    ->unique();
            $table->string('remember_token');
            $table->string('broj_mobitela');
            $table->string('email');
            $table->string('lozinka');
            $table->char('boja', 6)
                    ->default('ffffff');
            $table->integer('role_id')
                    ->unsigned()
                    ->nullable()
                    ->index();
            $table->dateTime('obracun')
                    ->nullable();
            $table->timestamps();
        });

        Schema::table('users', function(Blueprint $table) {
            $table->foreign('role_id')
                    ->references('id')->on('roles')
                    ->onUpdate('cascade')
                    ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table('users', function(Blueprint $table) {
            $table->dropForeign('users_role_id_foreign');
        });
        Schema::drop('users');
    }

}

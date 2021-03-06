<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePermissionRoleTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('permission_role', function(Blueprint $table)
		{
			$table->integer('permission_id')
			->unsigned()
			->index();
			$table->integer('role_id')
			->unsigned()
			->index();
		});

		Schema::table('permission_role', function(Blueprint $table)
		{
			$table->primary(array('role_id', 'permission_id'));
			$table->foreign('permission_id')
			->references('id')->on('permissions')
			->onDelete('cascade')
			->onUpdate('cascade');
			$table->foreign('role_id')
			->references('id')->on('roles')
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
		Schema::table('permission_role', function(Blueprint $table)
		{
			$table->dropForeign('permission_role_role_id_foreign');
			$table->dropForeign('permission_role_permission_id_foreign');
		});

		Schema::drop('permission_role');
	}

}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientGovernorateTable extends Migration {

	public function up()
	{
		Schema::create('client_governorate', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('client_id');
			$table->string('governorate_id');
		});
	}

	public function down()
	{
		Schema::drop('client_governorate');
	}
}
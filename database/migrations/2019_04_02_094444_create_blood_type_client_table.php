<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBloodTypeClientTable extends Migration {

	public function up()
	{
		Schema::create('blood_type_client', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('client_id');
			$table->string('blood_type_id');
		});
	}

	public function down()
	{
		Schema::drop('blood_type_client');
	}
}
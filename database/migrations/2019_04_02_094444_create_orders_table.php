<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateOrdersTable extends Migration {

	public function up()
	{
		Schema::create('orders', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('name');
			$table->string('hospital');
			$table->string('hospital_address');
			$table->string('details');
			$table->integer('age');
			$table->integer('latitude');
			$table->integer('langitude');
			$table->integer('city_id');
			$table->enum('blood_type', array('O-','O+','B-','B+','A-','A+','AB-','AB+'));
		});
	}

	public function down()
	{
		Schema::drop('orders');
	}
}

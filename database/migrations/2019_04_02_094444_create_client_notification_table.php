<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateClientNotificationTable extends Migration {

	public function up()
	{
		Schema::create('client_notification', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('client_id');
			$table->string('notification_id');
			$table->boolean('read');
		});
	}

	public function down()
	{
		Schema::drop('client_notification');
	}
}
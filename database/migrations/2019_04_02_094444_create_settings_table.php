<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateSettingsTable extends Migration {

	public function up()
	{
		Schema::create('settings', function(Blueprint $table) {
			$table->increments('id');
			$table->timestamps();
			$table->string('app_url');
			$table->text('about_app');
			$table->integer('phone');
			$table->string('facebook_url');
			$table->string('twitter_url');
			$table->string('instgram_url');
		});
	}

	public function down()
	{
		Schema::drop('settings');
	}
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{

    protected $table = 'settings';
    public $timestamps = true;
    protected $fillable = array('app_url', 'about_app', 'phone', 'facebook_url', 'twitter_url', 'instgram_url');

}

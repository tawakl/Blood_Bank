<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CN extends Model
{

    protected $table = 'client_notification';
    public $timestamps = true;
    protected $fillable = array('client_id', 'notification_id', 'read');

}

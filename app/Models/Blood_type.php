<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blood_type extends Model
{

    protected $table = 'blood_types';
    public $timestamps = true;
    protected $fillable = array('name');

    public function clients()
    {
        return $this->hasMany('App\Models\Client');
    }

    public function orders()
    {
        return $this->hasMany('App\Models\Order');
    }

}

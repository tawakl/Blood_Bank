<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    protected $table = 'orders';
    public $timestamps = true;
    protected $fillable = array('name', 'hospital', 'hospital_address', 'details', 'age', 'latitude', 'langitude', 'city_id', 'blood_type_id');

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function notifications()
    {
        return $this->hasMany('App\Models\Notification');
    }

    public function blood_type()
    {
        return $this->belongsTo('App\Models\Blood_type');
    }

}

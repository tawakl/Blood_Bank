<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{

    protected $table = 'clients';
    public $timestamps = true;
    protected $fillable = array('phone', 'password', 'name', 'email', 'birth_date', 'last_donation', 'city_id', 'blood_type','is_active','pin_code');

    public function posts()
    {
        return $this->belongsToMany('App\Models\Post');
    }

    public function city()
    {
        return $this->belongsTo('App\Models\City');
    }

    public function governorates()
    {
        return $this->belongsToMany('App\Models\Governorate');
    }

    public function notifications()
    {
        return $this->belongsToMany('App\Models\Notification');
    }

    public function blood_type()
    {
        return $this->belongsTo('App\Models\Blood_type');
    }

    public function blood_types()
    {
        return $this->belongsToMany('Blood_type');
    }
    public function tokens()
    {
        return $this->hasMany('App\Models\Token');
    }
    protected $hidden = [
        'password', 'api_token',
    ];

}

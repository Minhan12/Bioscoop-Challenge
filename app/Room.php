<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['amountofseats'];

	/**
	 * creates the link with the seats model
	 */
    public function seats()
    {
        return $this->hasMany('App/Seats');
    }
}

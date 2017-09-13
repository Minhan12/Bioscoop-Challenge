<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Seats extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['seat_nr', 'occupied', 'room', 'reserved'];

}

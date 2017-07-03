<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ManageAuth extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    protected $table = 'manage_auth';
    public $timestamps = false;

}

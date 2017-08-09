<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class GroupUser extends Model
{
    protected $dates = ['created_at'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $timestamps = false;

    public function __construct()
    {
        $this->table = 'group_user';
    }

}

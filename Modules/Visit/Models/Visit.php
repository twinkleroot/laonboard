<?php

namespace Modules\Visit\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $table = 'visits';

    public $timestamps = false;
}

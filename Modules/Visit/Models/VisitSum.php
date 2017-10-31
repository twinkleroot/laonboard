<?php

namespace Modules\Visit\Models;

use Illuminate\Database\Eloquent\Model;

class VisitSum extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public $table = 'visit_sums';

    public $timestamps = false;
}

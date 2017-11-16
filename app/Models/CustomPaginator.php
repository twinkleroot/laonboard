<?php

namespace App\Models;

use Illuminate\Pagination\LengthAwarePaginator;

class CustomPaginator extends LengthAwarePaginator
{
    public function __contruct($items, $total, $perPage, $currentPage = null, array $options = [])
    {
        parent::__contruct($items, $total, $perPage, $currentPage, $options);
    }

}

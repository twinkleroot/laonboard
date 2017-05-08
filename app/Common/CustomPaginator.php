<?php

namespace App\Common;
use Illuminate\Pagination\LengthAwarePaginator;

class CustomPaginator extends LengthAwarePaginator
{
    public function __contruct($items, $total, $perPage, $currentPage = null, array $options = [])
    {
        parent::__contruct($items, $total, $perPage, $currentPage, $options);
    }

    // public function setCurrentPage($currentPage, $lastPage)
    // {
    //     // The page number will get validated and adjusted if it either less than one
    //     // or greater than the last page available based on the count of the given
    //     // items array. If it's greater than the last, we'll give back the last.
    //     if (is_numeric($currentPage) && $currentPage > $lastPage) {
    //         return $lastPage > 0 ? $lastPage : 1;
    //     }
    //
    //     // $currentPage = $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    //
    //
    //     $this->currentPage = $this->isValidPageNumber($currentPage) ? (int) $currentPage : 1;
    //
    //     return $this->currentPage;
    // }
}

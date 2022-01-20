<?php

namespace App\Http\Traits;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

trait CustomPagination
{
    protected function paginate($items, $perPage = 10, $page = null, $options = []): LengthAwarePaginator
    {
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}

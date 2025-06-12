<?php

namespace App\Interfaces;

use Illuminate\Pagination\LengthAwarePaginator;

interface PropertyRepositoryInterface
{
    public function search(array $filters): LengthAwarePaginator;
}

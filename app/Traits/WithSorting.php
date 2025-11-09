<?php

namespace App\Traits;

trait WithSorting
{
    public $sortByField = '';
    public $sortDirection = 'asc';

    public function sortBy($field)
    {
        $this->sortDirection = $this->sortByField === $field
            ? $this->reverseSort()
            : 'asc';

        $this->sortByField = $field;
    }

    public function reverseSort()
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }
}

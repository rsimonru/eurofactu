<?php

namespace App\Traits;

use App\Models\User;

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
        if (isset($this->filter_name) && !empty($this->filter_name)) {
            $this->filter['sort'] = $this->sortByField;
            $this->filter['order'] = $this->sortDirection;
            User::saveFilters($this->filter_name, $this->filter);
        }
    }

    public function reverseSort()
    {
        return $this->sortDirection === 'asc'
            ? 'desc'
            : 'asc';
    }
}

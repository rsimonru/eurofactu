<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;

trait WithFilters
{
	public $filter;
	public $filter_component; // Son filtros que no se graban en base de datos y que sólo actuan en el componente activo.
	public $filter_labels;
	public $filter_defaults;
	public $filter_name = '';

	/**
     * Reset search
     */
    public function updatedFilter($value, $key): void
    {
        if ($key === 'search') {
            $this->searchRecords(true);
        }
    }

	public function searchRecords($modal = true)
	{
		if ($modal) {
			// $this->validate();
		}
		if (isset($this->filter['date']) && length($this->filter['date']) == 3) {
			$this->filter['date'][1] = $this->filter['date'][1] ? Carbon::createFromFormat('Y-m-d', $this->filter['date'][1])->format('Y-m-d') : null;
			$this->filter['date'][2] = $this->filter['date'][1] ? Carbon::createFromFormat('Y-m-d', $this->filter['date'][2])->format('Y-m-d') : null;
		}
        if (isset($this->filter_component['date']) && length($this->filter_component['date']) == 3) {
			$this->filter_component['date'][1] = $this->filter_component['date'][1] ? Carbon::createFromFormat('Y-m-d', $this->filter_component['date'][1])->format('Y-m-d') : null;
			$this->filter_component['date'][2] = $this->filter_component['date'][2] ? Carbon::createFromFormat('Y-m-d', $this->filter_component['date'][2])->format('Y-m-d') : null;
		}
		User::saveFilters($this->filter_name, $this->filter);
        $this->resetPage();
		if ($modal) {
			$this->modal('filter-records')->close();
		}
	}

	public function deleteLabelFilter($field)
	{
		$this->filter[$field] = config('filters.' . $this->filter_name . '.0.' . $field);
		User::saveFilters($this->filter_name, $this->filter);
		$this->dispatch('searchRecords');
	}

	public function deleteFilter()
	{
		$this->filter = config('filters.' . $this->filter_name . '.0');
        $methodVar = [get_class($this), 'initValues'];
        // dd(get_class($this));
        if (is_callable('initValues', false)) {
            $this->initValues();
        }
		User::saveFilters($this->filter_name, $this->filter);
        $this->resetPage();

		$this->modal('filter-records')->close();
	}

	public function getMergeFilters()
	{
        $filters = array_merge($this->filter, $this->filter_component ?? []);
        if (isset($filters['date']) && length($filters['date']) == 3) {
			$filters['date'][1] = $filters['date'][1] ? Carbon::createFromFormat('Y-m-d', $filters['date'][1])->format('Y-m-d') : null;
			$filters['date'][2] = $filters['date'][2] ? Carbon::createFromFormat('Y-m-d', $filters['date'][2])->format('Y-m-d') : null;
		}
		return $filters;
	}

	public function getFilters()
	{
		$errors = $this->getErrorBag();
		if (length($errors) == 0) {
			$filter = User::getFilters($this->filter_name);
			$this->filter = $filter['ufilters'];
			$this->filter_labels = $filter['flist'];
			$this->sortBy = $this->filter['sort'];
			$this->sortDirection = $this->filter['order'];

			if (!empty($this->filter['date'])) {
				$this->filter['date'][1] = $this->filter['date'][1] ? (new Carbon($this->filter['date'][1]))->format('Y-m-d') : null;
				$this->filter['date'][2] = $this->filter['date'][2] ? (new Carbon($this->filter['date'][2]))->format('Y-m-d') : null;
			}
            if (!empty($this->filter_component['date'])) {
				$this->filter_component['date'][1] = $this->filter_component['date'][1] ? (new Carbon($this->filter_component['date'][1]))->format('Y-m-d') : null;
				$this->filter_component['date'][2] = $this->filter_component['date'][2] ? (new Carbon($this->filter_component['date'][2]))->format('Y-m-d') : null;
			}

			if (!empty($filter['filters_defaults'][$this->filter_name])) {
				$this->filters_defaults = $filter['filters_defaults'][$this->filter_name];
			} else {
				$this->filters_defaults = [];
			}
		}
	}
}

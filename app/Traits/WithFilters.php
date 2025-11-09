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

	public function searchRecords($modal = true)
	{
		if ($modal) {
			// $this->validate();
		}
		if (isset($this->filter['date']) && length($this->filter['date']) == 3) {
			$this->filter['date'][1] = Carbon::createFromFormat(config('locale.languages.'.app()->getLocale().'.5'), $this->filter['date'][1])->format('Y-m-d');
			$this->filter['date'][2] = Carbon::createFromFormat(config('locale.languages.'.app()->getLocale().'.5'), $this->filter['date'][2])->format('Y-m-d');
		}
        if (isset($this->filter_component['date']) && length($this->filter_component['date']) == 3) {
			$this->filter_component['date'][1] = Carbon::createFromFormat(config('locale.languages.'.app()->getLocale().'.5'), $this->filter_component['date'][1])->format('Y-m-d');
			$this->filter_component['date'][2] = Carbon::createFromFormat(config('locale.languages.'.app()->getLocale().'.5'), $this->filter_component['date'][2])->format('Y-m-d');
		}
		User::saveFilters($this->filter_name, $this->filter);
		// $this->emit('searchRecords');
        $this->resetPage();
		if ($modal) {
			// $this->closeModal();
			//$this->dispatchBrowserEvent('filterModal', [false]);
            $this->dispatchBrowserEvent('closeModal', ['modalID' => 'filterModal']);
		}
	}

	public function deleteLabelFilter($field)
	{
		$this->filter[$field] = config('filters.' . $this->filter_name . '.0.' . $field);
		User::saveFilters($this->filter_name, $this->filter);
		$this->emit('searchRecords');
	}

	public function deleteFilter()
	{
		$this->filter = config('filters.' . $this->filter_name . '.0');
        $methodVar = [get_class($this),'initValues'];
        // dd(get_class($this));
        if (is_callable('initValues', false)) {
            $this->initValues();
        }
		User::saveFilters($this->filter_name, $this->filter);
        $this->resetPage();
		// $this->emit('searchRecords');
		//$this->dispatchBrowserEvent('filterModal', ['open' => false]);
        $this->dispatchBrowserEvent('closeModal', ['modalID' => 'filterModal']);
		// $this->closeModal();
		// $this->dispatchBrowserEvent('closeModal', ['modalID' => 'searchRecords']);
	}

	public function getMergeFilters()
	{
        $filters = array_merge($this->filter, $this->filter_component ?? []);
        if (isset($filters['date']) && length($filters['date']) == 3) {
			$filters['date'][1] = Carbon::createFromFormat(config('locale.languages.'.app()->getLocale().'.5'), $filters['date'][1])->format('Y-m-d');
			$filters['date'][2] = Carbon::createFromFormat(config('locale.languages.'.app()->getLocale().'.5'), $filters['date'][2])->format('Y-m-d');
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
				$this->filter['date'][1] = (new Carbon($this->filter['date'][1]))->format(config('locale.languages.'.app()->getLocale().'.5'));
				$this->filter['date'][2] = (new Carbon($this->filter['date'][2]))->format(config('locale.languages.'.app()->getLocale().'.5'));
			}
            if (!empty($this->filter_component['date'])) {
				$this->filter_component['date'][1] = (new Carbon($this->filter_component['date'][1]))->format(config('locale.languages.'.app()->getLocale().'.5'));
				$this->filter_component['date'][2] = (new Carbon($this->filter_component['date'][2]))->format(config('locale.languages.'.app()->getLocale().'.5'));
			}

			if (!empty($filter['filters_defaults'][$this->filter_name])) {
				$this->filters_defaults = $filter['filters_defaults'][$this->filter_name];
			} else {
				$this->filters_defaults = [];
			}
		}
	}
}

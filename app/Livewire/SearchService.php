<?php

namespace App\Livewire;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class SearchService extends Component
{
    use WithPagination;

    public $search = '';

    public function updateSearch($value)
    {
        $this->search = $value;
        
    }

    public function render()
    {
        $services = Service::where('name', 'like', '%' . $this->search . '%')->paginate(10);

        return view('livewire.search-service', ['services' => $services]);
    }
}

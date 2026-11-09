<?php

namespace App\Livewire;

use App\Models\Sponser;
use Livewire\Component;
use Livewire\WithPagination;

class SearchSponser extends Component
{
    use WithPagination;

    public $search = '';

    public function updateSearch($value)
    {
        $this->search = $value;
    }

    
    public function render()
    {
        $sponsers = Sponser::where('title', 'like', '%' . $this->search . '%')
                            ->orWhere('type', 'like', '%' . $this->search . '%')
                            ->paginate(10);

        return view('livewire.search-sponser', ['sponsers' => $sponsers]);
    }
}

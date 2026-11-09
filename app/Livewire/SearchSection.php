<?php

namespace App\Livewire;

use App\Models\Section;
use Livewire\Component;
use Livewire\WithPagination;

class SearchSection extends Component
{
    use WithPagination;

    public $search = '';

    public function updateSearch($value)
    {
        $this->search = $value;
    }


    public function render()
    {
        $sections = Section::where('name', 'like', '%' . $this->search . '%')->paginate(10);

        return view('livewire.search-section', ['sections' => $sections]);
    }
}

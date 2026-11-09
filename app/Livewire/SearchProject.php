<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class SearchProject extends Component
{
    use WithPagination;

    public $search = '';



    public function updateSearch($value)
    {
        $this->search = $value;
    }

    public function render()
    {
        $projects = Project::where(function ($query) {

                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })

                ->orWhereHas('section', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })

                ->orWhereHas('service', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->paginate(10);

        return view('livewire.search-project', ['projects' => $projects]);
    }
}

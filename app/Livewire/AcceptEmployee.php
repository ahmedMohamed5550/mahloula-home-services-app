<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;

class AcceptEmployee extends Component
{
    use WithPagination;

    public $search = '';

    public function updateSearch($value)
    {
        $this->search = $value;
    }

    public function render()
    {
        $employees = Employee::where('checkByAdmin', 'accepted')
            ->where(function ($query) {

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

        return view('livewire.accept-employee', ['employees' => $employees]);
    }
}

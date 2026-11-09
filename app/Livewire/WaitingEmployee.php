<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Employee;
use Livewire\WithPagination;

class WaitingEmployee extends Component
{
    use WithPagination;

    public $search = '';

    public function updateSearch($value)
    {
        $this->search = $value;
    }

    public function render()
    {
        $employees = Employee::where('checkByAdmin', 'waiting')
            ->where(function ($query) {
                // البحث في اسم المستخدم
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%');
                })
                // البحث في اسم القسم
                ->orWhereHas('section', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%'); // استبدل 'name' باسم الحقل المناسب للقسم
                })
                // البحث في اسم الخدمة
                ->orWhereHas('service', function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%'); // استبدل 'name' باسم الحقل المناسب للخدمة
                });
            })
            ->paginate(10);

        return view('livewire.waiting-employee', ['employees' => $employees]);
    }

}

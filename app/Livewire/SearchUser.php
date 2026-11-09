<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class SearchUser extends Component
{
    use WithPagination;

    public $search = '';

    public function updateSearch($value)
    {
        $this->search = $value;
    }

    public function render()
    {
        $users = User::where('userType', 'user')
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.search-user', ['users' => $users]);
    }
}

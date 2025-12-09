<?php

namespace App\Livewire\Admin\TransactionHistory;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class HistoryIndex extends Component
{
    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('admin'), 403);
    }

    public function render()
    {
        return view('livewire.admin.transaction-history.history-index');
    }
}

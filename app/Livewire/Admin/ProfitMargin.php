<?php

namespace App\Livewire\Admin;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class ProfitMargin extends Component
{
    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        // Allow both admin and superadmin
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasAnyRole(['admin', 'superadmin']), 403);
    }

    public function render()
    {
        return view('livewire.admin.profit-margin');
    }
}

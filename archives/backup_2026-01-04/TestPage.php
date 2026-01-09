<?php

namespace App\Livewire;

use Livewire\Component;

class TestPage extends Component
{
    public $count = 0;

    public function increment()
    {
        $this->count++;
    }

    public function render()
    {
        return view('livewire.test-page');
    }
}

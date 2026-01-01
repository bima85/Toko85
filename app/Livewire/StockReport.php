<?php

namespace App\Livewire;

use Livewire\Component;

class StockReport extends Component
{
    public $activeTab = 'store'; // 'store' or 'warehouse'

    public function getButtonClass($tab)
    {
        return $this->activeTab === $tab ? 'btn btn-primary' : 'btn btn-outline-primary';
    }

    public function setActiveTab($tab)
    {
        \Log::info('TestStock.setActiveTab called', ['tab' => $tab]);
        $this->activeTab = $tab;
        \Log::info('TestStock.setActiveTab updated', ['activeTab' => $this->activeTab]);
    }

    public function render()
    {
        return view('livewire.stock-report');
    }
}

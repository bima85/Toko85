<?php

namespace App\Livewire\Admin\TransactionHistory;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.admin')]
class HistoryIndex extends Component
{
    public array $selectedTransactions = [];
    public bool $selectAll = false;

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('admin'), 403);
    }

    #[\Livewire\Attributes\On('toggleSelect')]
    public function toggleSelect($transactionId = null)
    {
        if ($transactionId === null) {
            return;
        }

        $transactionId = (int) $transactionId;
        if (in_array($transactionId, $this->selectedTransactions)) {
            $this->selectedTransactions = array_filter(
                $this->selectedTransactions,
                fn($id) => $id != $transactionId
            );
        } else {
            $this->selectedTransactions[] = $transactionId;
        }
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            // When select all is checked, we'll let frontend populate selectedTransactions
            // via the setAllTransactionIds method
            // Don't dispatch anything here to avoid re-render
        } else {
            // When unchecked, clear all selections
            $this->selectedTransactions = [];
        }
    }

    /**
     * Directly set selected transaction IDs from frontend
     * This is called via Livewire event dispatch
     */
    #[\Livewire\Attributes\On('setAllTransactionIds')]
    public function setAllTransactionIds($ids = null)
    {
        // Handle parameter - in Livewire v3, it comes as named parameter
        if ($ids === null) {
            return;
        }

        // Convert to array if needed
        if (is_array($ids)) {
            $this->selectedTransactions = array_values(array_map('intval', $ids));
        }
    }

    public function deleteSelected($ids = [])
    {
        // Accept IDs as parameter from JavaScript AJAX call
        if (is_array($ids) && !empty($ids)) {
            $this->selectedTransactions = array_map('intval', $ids);
        }

        if (empty($this->selectedTransactions)) {
            session()->flash('error', 'Tidak ada transaksi yang dipilih');
            return;
        }

        try {
            // Delete selected transactions
            \App\Models\TransactionHistory::whereIn('id', $this->selectedTransactions)->delete();

            session()->flash('message', 'Berhasil menghapus ' . count($this->selectedTransactions) . ' transaksi');
            $this->selectedTransactions = [];
            $this->selectAll = false;
            // notify frontend to reload and clear any UI selection
            $this->dispatch('reloadTransactionsTable');
            $this->dispatch('clearSelection');
        } catch (\Exception $e) {
            session()->flash('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Collect checked transaction IDs from frontend before deleting
     * This method is called via JavaScript before deleteSelected
     */
    public function collectCheckedIds()
    {
        // IDs akan dikirim via JavaScript setelah user click delete button
        // We'll trigger a JavaScript function that collects checkboxes
        $this->dispatch('collectAndDelete');
    }

    public function clearSelection()
    {
        $this->selectedTransactions = [];
        $this->selectAll = false;
        // notify frontend to clear checkboxes
        $this->dispatch('clearSelection');
    }

    public function render()
    {
        return view('livewire.admin.transaction-history.history-index');
    }
}

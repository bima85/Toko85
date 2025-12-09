<?php

namespace App\Livewire\Admin;

use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Units extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_unit;
    public $nama_unit;
    public $description;
    public $parent_unit_id;
    public $conversion_value;
    public $is_base_unit = false;
    public $editingUnitId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedUnit = null;
    public $confirmingDelete = false;
    public $unitToDelete = null;

    protected $rules = [
        'kode_unit' => 'required|string|max:50|unique:units,kode_unit',
        'nama_unit' => 'required|string|max:255',
        'description' => 'nullable|string',
        'parent_unit_id' => 'nullable|exists:units,id',
        'conversion_value' => 'nullable|numeric|min:0',
        'is_base_unit' => 'boolean',
    ];

    protected $listeners = ['close-form' => 'cancel', 'cancel-form' => 'cancel'];

    public function mount()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        abort_unless($user && method_exists($user, 'hasRole') && $user->hasRole('admin'), 403);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit($id)
    {
        $unit = Unit::findOrFail($id);
        $this->editingUnitId = $unit->id;
        $this->kode_unit = $unit->kode_unit;
        $this->nama_unit = $unit->nama_unit;
        $this->description = $unit->description;
        $this->parent_unit_id = $unit->parent_unit_id;
        $this->conversion_value = $unit->conversion_value;
        $this->is_base_unit = $unit->is_base_unit;
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedUnit = Unit::findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedUnit = null;
    }

    public function save()
    {
        $rules = [
            'nama_unit' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_unit_id' => 'nullable|exists:units,id',
            'conversion_value' => 'nullable|numeric|min:0',
            'is_base_unit' => 'boolean',
            'kode_unit' => 'required|string|max:50|unique:units,kode_unit' . ($this->editingUnitId ? (',' . $this->editingUnitId) : ''),
        ];

        $this->validate($rules);

        if ($this->editingUnitId) {
            $unit = Unit::findOrFail($this->editingUnitId);
            $unit->kode_unit = $this->kode_unit;
            $unit->nama_unit = $this->nama_unit;
            $unit->description = $this->description;
            $unit->parent_unit_id = $this->parent_unit_id;
            $unit->conversion_value = $this->conversion_value;
            $unit->is_base_unit = $this->is_base_unit;
            $unit->save();
            session()->flash('message', 'Unit diperbarui.');
        } else {
            Unit::create([
                'kode_unit' => $this->kode_unit,
                'nama_unit' => $this->nama_unit,
                'description' => $this->description,
                'parent_unit_id' => $this->parent_unit_id,
                'conversion_value' => $this->conversion_value,
                'is_base_unit' => $this->is_base_unit,
            ]);
            session()->flash('message', 'Unit dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function resetForm()
    {
        $this->kode_unit = null;
        $this->nama_unit = null;
        $this->description = null;
        $this->parent_unit_id = null;
        $this->conversion_value = null;
        $this->is_base_unit = false;
        $this->editingUnitId = null;
        $this->showForm = false;
    }

    public function cancel()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function confirmDelete($id)
    {
        $this->confirmingDelete = true;
        $this->unitToDelete = $id;
    }

    public function cancelDelete()
    {
        $this->confirmingDelete = false;
        $this->unitToDelete = null;
    }

    public function deleteConfirmed()
    {
        if ($this->unitToDelete) {
            $unit = Unit::findOrFail($this->unitToDelete);
            $unit->delete();
            session()->flash('message', 'Unit dihapus.');
            $this->cancelDelete();
        }
    }

    public function render()
    {
        $units = Unit::where('nama_unit', 'like', '%' . $this->search . '%')
            ->orWhere('kode_unit', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        $availableUnits = Unit::where('id', '!=', $this->editingUnitId ?? 0)
            ->orderBy('nama_unit')
            ->get();

        return view('livewire.admin.units', [
            'units' => $units,
            'availableUnits' => $availableUnits,
        ])->layout('layouts.admin');
    }
}

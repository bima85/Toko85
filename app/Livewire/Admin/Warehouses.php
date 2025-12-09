<?php

namespace App\Livewire\Admin;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Warehouses extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_gudang;
    public $nama_gudang;
    public $alamat;
    public $telepon;
    public $pic;
    public $keterangan;
    public $editingWarehouseId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedWarehouse = null;

    protected $rules = [
        'kode_gudang' => 'required|string|max:50|unique:warehouses,kode_gudang',
        'nama_gudang' => 'required|string|max:255',
        'alamat' => 'nullable|string',
        'telepon' => 'nullable|string|max:20',
        'pic' => 'nullable|string|max:255',
        'keterangan' => 'nullable|string',
    ];

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
        $warehouse = Warehouse::findOrFail($id);
        $this->editingWarehouseId = $warehouse->id;
        $this->kode_gudang = $warehouse->kode_gudang;
        $this->nama_gudang = $warehouse->nama_gudang;
        $this->alamat = $warehouse->alamat;
        $this->telepon = $warehouse->telepon;
        $this->pic = $warehouse->pic;
        $this->keterangan = $warehouse->keterangan;
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedWarehouse = Warehouse::findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedWarehouse = null;
    }

    public function save()
    {
        $rules = [
            'nama_gudang' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'pic' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'kode_gudang' => 'required|string|max:50|unique:warehouses,kode_gudang' . ($this->editingWarehouseId ? (',' . $this->editingWarehouseId) : ''),
        ];

        $this->validate($rules);

        if ($this->editingWarehouseId) {
            $warehouse = Warehouse::findOrFail($this->editingWarehouseId);
            $warehouse->kode_gudang = $this->kode_gudang;
            $warehouse->nama_gudang = $this->nama_gudang;
            $warehouse->alamat = $this->alamat;
            $warehouse->telepon = $this->telepon;
            $warehouse->pic = $this->pic;
            $warehouse->keterangan = $this->keterangan;
            $warehouse->save();
            session()->flash('message', 'Gudang diperbarui.');
        } else {
            Warehouse::create([
                'kode_gudang' => $this->kode_gudang,
                'nama_gudang' => $this->nama_gudang,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
                'pic' => $this->pic,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('message', 'Gudang dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function resetForm()
    {
        $this->kode_gudang = null;
        $this->nama_gudang = null;
        $this->alamat = null;
        $this->telepon = null;
        $this->pic = null;
        $this->keterangan = null;
        $this->editingWarehouseId = null;
        $this->showForm = false;
    }

    public function delete($id)
    {
        $warehouse = Warehouse::findOrFail($id);
        $warehouse->delete();
        session()->flash('message', 'Gudang dihapus.');
    }

    public function render()
    {
        $warehouses = Warehouse::where('nama_gudang', 'like', '%' . $this->search . '%')
            ->orWhere('kode_gudang', 'like', '%' . $this->search . '%')
            ->orWhere('pic', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.warehouses', [
            'warehouses' => $warehouses,
        ])->layout('layouts.admin');
    }
}

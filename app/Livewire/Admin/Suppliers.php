<?php

namespace App\Livewire\Admin;

use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Suppliers extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_supplier;
    public $nama_supplier;
    public $alamat;
    public $telepon;
    public $email;
    public $keterangan;
    public $editingSupplierId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedSupplier = null;

    protected $rules = [
        'kode_supplier' => 'required|string|max:50|unique:suppliers,kode_supplier',
        'nama_supplier' => 'required|string|max:255',
        'alamat' => 'nullable|string',
        'telepon' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
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
        $supplier = Supplier::findOrFail($id);
        $this->editingSupplierId = $supplier->id;
        $this->kode_supplier = $supplier->kode_supplier;
        $this->nama_supplier = $supplier->nama_supplier;
        $this->alamat = $supplier->alamat;
        $this->telepon = $supplier->telepon;
        $this->email = $supplier->email;
        $this->keterangan = $supplier->keterangan;
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedSupplier = Supplier::findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedSupplier = null;
    }

    public function save()
    {
        $rules = [
            'nama_supplier' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'keterangan' => 'nullable|string',
            'kode_supplier' => 'required|string|max:50|unique:suppliers,kode_supplier' . ($this->editingSupplierId ? (',' . $this->editingSupplierId) : ''),
        ];

        $this->validate($rules);

        if ($this->editingSupplierId) {
            $supplier = Supplier::findOrFail($this->editingSupplierId);
            $supplier->kode_supplier = $this->kode_supplier;
            $supplier->nama_supplier = $this->nama_supplier;
            $supplier->alamat = $this->alamat;
            $supplier->telepon = $this->telepon;
            $supplier->email = $this->email;
            $supplier->keterangan = $this->keterangan;
            $supplier->save();
            session()->flash('message', 'Pemasok diperbarui.');
        } else {
            Supplier::create([
                'kode_supplier' => $this->kode_supplier,
                'nama_supplier' => $this->nama_supplier,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
                'email' => $this->email,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('message', 'Pemasok dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function resetForm()
    {
        $this->kode_supplier = null;
        $this->nama_supplier = null;
        $this->alamat = null;
        $this->telepon = null;
        $this->email = null;
        $this->keterangan = null;
        $this->editingSupplierId = null;
        $this->showForm = false;
    }

    public function delete($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();
        session()->flash('message', 'Pemasok dihapus.');
    }

    public function render()
    {
        $suppliers = Supplier::where('nama_supplier', 'like', '%' . $this->search . '%')
            ->orWhere('kode_supplier', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.suppliers', [
            'suppliers' => $suppliers,
        ])->layout('layouts.admin');
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Stores extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_toko;
    public $nama_toko;
    public $alamat;
    public $telepon;
    public $pic;
    public $tipe_toko = 'retail';
    public $keterangan;
    public $editingStoreId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedStore = null;

    protected $rules = [
        'kode_toko' => 'required|string|max:50|unique:stores,kode_toko',
        'nama_toko' => 'required|string|max:255',
        'alamat' => 'nullable|string',
        'telepon' => 'nullable|string|max:20',
        'pic' => 'nullable|string|max:255',
        'tipe_toko' => 'required|in:retail,wholesale,online,outlet',
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
        $store = Store::findOrFail($id);
        $this->editingStoreId = $store->id;
        $this->kode_toko = $store->kode_toko;
        $this->nama_toko = $store->nama_toko;
        $this->alamat = $store->alamat;
        $this->telepon = $store->telepon;
        $this->pic = $store->pic;
        $this->tipe_toko = $store->tipe_toko;
        $this->keterangan = $store->keterangan;
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedStore = Store::findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedStore = null;
    }

    public function save()
    {
        $rules = [
            'nama_toko' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'pic' => 'nullable|string|max:255',
            'tipe_toko' => 'required|in:retail,wholesale,online,outlet',
            'keterangan' => 'nullable|string',
            'kode_toko' => 'required|string|max:50|unique:stores,kode_toko' . ($this->editingStoreId ? (',' . $this->editingStoreId) : ''),
        ];

        $this->validate($rules);

        if ($this->editingStoreId) {
            $store = Store::findOrFail($this->editingStoreId);
            $store->kode_toko = $this->kode_toko;
            $store->nama_toko = $this->nama_toko;
            $store->alamat = $this->alamat;
            $store->telepon = $this->telepon;
            $store->pic = $this->pic;
            $store->tipe_toko = $this->tipe_toko;
            $store->keterangan = $this->keterangan;
            $store->save();
            session()->flash('message', 'Toko diperbarui.');
        } else {
            Store::create([
                'kode_toko' => $this->kode_toko,
                'nama_toko' => $this->nama_toko,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
                'pic' => $this->pic,
                'tipe_toko' => $this->tipe_toko,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('message', 'Toko dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function resetForm()
    {
        $this->kode_toko = null;
        $this->nama_toko = null;
        $this->alamat = null;
        $this->telepon = null;
        $this->pic = null;
        $this->tipe_toko = 'retail';
        $this->keterangan = null;
        $this->editingStoreId = null;
        $this->showForm = false;
    }

    public function delete($id)
    {
        $store = Store::findOrFail($id);
        $store->delete();
        session()->flash('message', 'Toko dihapus.');
    }

    public function render()
    {
        $stores = Store::where('nama_toko', 'like', '%' . $this->search . '%')
            ->orWhere('kode_toko', 'like', '%' . $this->search . '%')
            ->orWhere('pic', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.stores', [
            'stores' => $stores,
        ])->layout('layouts.admin');
    }
}

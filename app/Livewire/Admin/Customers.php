<?php

namespace App\Livewire\Admin;

use App\Models\Customer;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_pelanggan;
    public $nama_pelanggan;
    public $alamat;
    public $telepon;
    public $email;
    public $keterangan;
    public $editingCustomerId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedCustomer = null;

    protected $rules = [
        'kode_pelanggan' => 'required|string|max:50|unique:customers,kode_pelanggan',
        'nama_pelanggan' => 'required|string|max:255',
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
        $customer = Customer::findOrFail($id);
        $this->editingCustomerId = $customer->id;
        $this->kode_pelanggan = $customer->kode_pelanggan;
        $this->nama_pelanggan = $customer->nama_pelanggan;
        $this->alamat = $customer->alamat;
        $this->telepon = $customer->telepon;
        $this->email = $customer->email;
        $this->keterangan = $customer->keterangan;
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedCustomer = Customer::findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedCustomer = null;
    }

    public function save()
    {
        $rules = [
            'nama_pelanggan' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'telepon' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'keterangan' => 'nullable|string',
            'kode_pelanggan' => 'required|string|max:50|unique:customers,kode_pelanggan' . ($this->editingCustomerId ? (',' . $this->editingCustomerId) : ''),
        ];

        $this->validate($rules);

        if ($this->editingCustomerId) {
            $customer = Customer::findOrFail($this->editingCustomerId);
            $customer->kode_pelanggan = $this->kode_pelanggan;
            $customer->nama_pelanggan = $this->nama_pelanggan;
            $customer->alamat = $this->alamat;
            $customer->telepon = $this->telepon;
            $customer->email = $this->email;
            $customer->keterangan = $this->keterangan;
            $customer->save();
            session()->flash('message', 'Pelanggan diperbarui.');
        } else {
            Customer::create([
                'kode_pelanggan' => $this->kode_pelanggan,
                'nama_pelanggan' => $this->nama_pelanggan,
                'alamat' => $this->alamat,
                'telepon' => $this->telepon,
                'email' => $this->email,
                'keterangan' => $this->keterangan,
            ]);
            session()->flash('message', 'Pelanggan dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function resetForm()
    {
        $this->kode_pelanggan = null;
        $this->nama_pelanggan = null;
        $this->alamat = null;
        $this->telepon = null;
        $this->email = null;
        $this->keterangan = null;
        $this->editingCustomerId = null;
        $this->showForm = false;
    }

    public function delete($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();
        session()->flash('message', 'Pelanggan dihapus.');
    }

    public function render()
    {
        $customers = Customer::where('nama_pelanggan', 'like', '%' . $this->search . '%')
            ->orWhere('kode_pelanggan', 'like', '%' . $this->search . '%')
            ->orWhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.admin.customers', [
            'customers' => $customers,
        ])->layout('layouts.admin');
    }
}

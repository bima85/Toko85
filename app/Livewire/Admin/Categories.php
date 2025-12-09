<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Categories extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_kategori;
    public $nama_kategori;
    public $description;
    public $editingCategoryId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedCategory = null;

    protected $rules = [
        'kode_kategori' => 'required|string|max:50|unique:categories,kode_kategori',
        'nama_kategori' => 'required|string|max:255',
        'description' => 'nullable|string',
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
        $this->editingCategoryId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->kode_kategori = $category->kode_kategori;
        $this->nama_kategori = $category->nama_kategori;
        $this->description = $category->description;
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedCategory = Category::findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedCategory = null;
    }

    public function save()
    {
        // Dynamic validation rules
        $rules = [
            'nama_kategori' => 'required|string|max:255',
            'description' => 'nullable|string',
            'kode_kategori' => 'required|string|max:50|unique:categories,kode_kategori' . ($this->editingCategoryId ? (',' . $this->editingCategoryId) : ''),
        ];

        $this->validate($rules);

        if ($this->editingCategoryId) {
            $category = Category::findOrFail($this->editingCategoryId);
            $category->kode_kategori = $this->kode_kategori;
            $category->nama_kategori = $this->nama_kategori;
            $category->description = $this->description;
            $category->save();
            session()->flash('message', 'Kategori diperbarui.');
        } else {
            Category::create([
                'kode_kategori' => $this->kode_kategori,
                'nama_kategori' => $this->nama_kategori,
                'description' => $this->description,
            ]);
            session()->flash('message', 'Kategori dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();
        session()->flash('message', 'Kategori dihapus.');
    }

    public function resetForm()
    {
        $this->kode_kategori = null;
        $this->nama_kategori = null;
        $this->description = null;
        $this->editingCategoryId = null;
        $this->showForm = false;
    }

    public function render()
    {
        $query = Category::query();
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_kategori', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_kategori', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $categories = $query->orderBy('id', 'desc')->paginate(10);

        return view('livewire.admin.categories', [
            'categories' => $categories,
        ])->layout('layouts.admin');
        /** @phpstan-ignore-next-line */
    }
}

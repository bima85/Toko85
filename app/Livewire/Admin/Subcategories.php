<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Subcategories extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_subkategori;
    public $nama_subkategori;
    public $description;
    public $category_id;
    public $editingSubcategoryId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedSubcategory = null;

    protected $rules = [
        'kode_subkategori' => 'required|string|max:50|unique:subcategories,kode_subkategori',
        'nama_subkategori' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
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
        $this->editingSubcategoryId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $this->editingSubcategoryId = $subcategory->id;
        $this->kode_subkategori = $subcategory->kode_subkategori;
        $this->nama_subkategori = $subcategory->nama_subkategori;
        $this->description = $subcategory->description;
        $this->category_id = $subcategory->category_id;
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedSubcategory = Subcategory::with('category')->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedSubcategory = null;
    }

    public function save()
    {
        // Dynamic validation rules
        $rules = [
            'nama_subkategori' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'kode_subkategori' => 'required|string|max:50|unique:subcategories,kode_subkategori' . ($this->editingSubcategoryId ? (',' . $this->editingSubcategoryId) : ''),
        ];

        $this->validate($rules);

        if ($this->editingSubcategoryId) {
            $subcategory = Subcategory::findOrFail($this->editingSubcategoryId);
            $subcategory->kode_subkategori = $this->kode_subkategori;
            $subcategory->nama_subkategori = $this->nama_subkategori;
            $subcategory->description = $this->description;
            $subcategory->category_id = $this->category_id;
            $subcategory->save();
            session()->flash('message', 'Subkategori diperbarui.');
        } else {
            Subcategory::create([
                'kode_subkategori' => $this->kode_subkategori,
                'nama_subkategori' => $this->nama_subkategori,
                'description' => $this->description,
                'category_id' => $this->category_id,
            ]);
            session()->flash('message', 'Subkategori dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();
        session()->flash('message', 'Subkategori dihapus.');
    }

    public function resetForm()
    {
        $this->kode_subkategori = null;
        $this->nama_subkategori = null;
        $this->description = null;
        $this->category_id = null;
        $this->editingSubcategoryId = null;
        $this->showForm = false;
    }

    public function render()
    {
        $query = Subcategory::with('category');
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_subkategori', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_subkategori', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('nama_kategori', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $subcategories = $query->orderBy('id', 'desc')->paginate(10);

        $categories = Category::orderBy('nama_kategori')->get();

        return view('livewire.admin.subcategories', [
            'subcategories' => $subcategories,
            'categories' => $categories,
        ])->layout('layouts.admin');
        /** @phpstan-ignore-next-line */
    }
}

<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use App\Models\Product;
use App\Models\Subcategory;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Products extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $kode_produk;
    public $nama_produk;
    public $description;
    public $category_id;
    public $subcategory_id;
    public $editingProductId = null;
    public $showForm = false;
    public $showModal = false;
    public $selectedProduct = null;
    public $subcategories = [];

    protected $rules = [
        'kode_produk' => 'required|string|max:50|unique:products,kode_produk',
        'nama_produk' => 'required|string|max:255',
        'description' => 'nullable|string',
        'category_id' => 'required|exists:categories,id',
        'subcategory_id' => 'nullable|exists:subcategories,id',
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

    public function updatedCategoryId($value)
    {
        $this->subcategory_id = null;
        $this->subcategories = Subcategory::where('category_id', $value)->orderBy('nama_subkategori')->get();
    }

    public function updatedNamaProduk($value)
    {
        // Generate kode_produk otomatis dari nama produk
        if ($value && !$this->editingProductId) {
            // Ambil 3 karakter pertama dari nama produk, convert to uppercase
            $prefix = strtoupper(substr(str_replace([' ', '-'], '', $value), 0, 3));

            // Hitung produk dengan prefix yang sama
            $count = Product::where('kode_produk', 'like', $prefix . '%')->count();

            // Format: PREFIX_001_NAMASINGKAT (misal: MAW_001, MAW_002)
            $namaShort = strtoupper(str_replace([' ', '-'], '', $value));
            $this->kode_produk = $prefix . '_' . str_pad($count + 1, 3, '0', STR_PAD_LEFT) . '_' . substr($namaShort, 0, 10);
        }
    }

    public function create()
    {
        $this->resetForm();
        $this->editingProductId = null;
        $this->showForm = true;
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->editingProductId = $product->id;
        $this->kode_produk = $product->kode_produk;
        $this->nama_produk = $product->nama_produk;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->subcategory_id = $product->subcategory_id;
        $this->updatedCategoryId($product->category_id); // Load subcategories
        $this->showForm = true;
    }

    public function show($id)
    {
        $this->selectedProduct = Product::with(['category', 'subcategory'])->findOrFail($id);
        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedProduct = null;
    }

    public function save()
    {
        // Dynamic validation rules
        $rules = [
            'nama_produk' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'nullable|exists:subcategories,id',
        ];

        // Jika editing, kode_produk harus unique kecuali milik produk itu sendiri
        // Jika create, kode_produk di-generate otomatis, tetap harus di-validate unique
        if ($this->editingProductId) {
            $rules['kode_produk'] = 'required|string|max:50|unique:products,kode_produk,' . $this->editingProductId;
        } else {
            // Untuk create, kode_produk di-generate otomatis, tapi masih harus unique
            if (!$this->kode_produk) {
                // Jika kode tidak ter-generate (belum ada nama produk), set error
                $this->addError('nama_produk', 'Nama produk harus diisi terlebih dahulu');
                return;
            }
            $rules['kode_produk'] = 'required|string|max:50|unique:products,kode_produk';
        }

        $this->validate($rules);

        if ($this->editingProductId) {
            $product = Product::findOrFail($this->editingProductId);
            $product->kode_produk = $this->kode_produk;
            $product->nama_produk = $this->nama_produk;
            $product->description = $this->description;
            $product->category_id = $this->category_id;
            $product->subcategory_id = $this->subcategory_id;
            $product->save();
            session()->flash('message', 'Produk diperbarui.');
        } else {
            Product::create([
                'kode_produk' => $this->kode_produk,
                'nama_produk' => $this->nama_produk,
                'description' => $this->description,
                'category_id' => $this->category_id,
                'subcategory_id' => $this->subcategory_id,
            ]);
            session()->flash('message', 'Produk dibuat.');
        }

        $this->resetForm();
        $this->showForm = false;
    }

    public function delete($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        session()->flash('message', 'Produk dihapus.');
    }

    public function resetForm()
    {
        $this->kode_produk = null;
        $this->nama_produk = null;
        $this->description = null;
        $this->category_id = null;
        $this->subcategory_id = null;
        $this->editingProductId = null;
        $this->showForm = false;
    }

    public function render()
    {
        $query = Product::with(['category', 'subcategory']);
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('kode_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('nama_produk', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('category', function ($q) {
                        $q->where('nama_kategori', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('subcategory', function ($q) {
                        $q->where('nama_subkategori', 'like', '%' . $this->search . '%');
                    });
            });
        }

        $products = $query->orderBy('id', 'desc')->paginate(10);

        $categories = Category::orderBy('nama_kategori')->get();

        return view('livewire.admin.products', [
            'products' => $products,
            'categories' => $categories,
            'subcategories' => $this->subcategories,
        ])->layout('layouts.admin');
        /** @phpstan-ignore-next-line */
    }
}

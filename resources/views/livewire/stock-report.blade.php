<div>
  <button class="btn btn-primary mr-2" wire:click="setActiveTab('store')" type="button">
    Stok Toko
  </button>
  <button class="btn btn-outline-primary" wire:click="setActiveTab('warehouse')" type="button">
    Stok Gudang
  </button>
  <p>Active Tab: {{ $activeTab }}</p>

  <div id="store-content" style="display: {{ $activeTab === 'store' ? 'block' : 'none' }}">
    Stok Toko content
  </div>
  <div id="warehouse-content" style="display: {{ $activeTab === 'warehouse' ? 'block' : 'none' }}">
    Stok Gudang content
  </div>
</div>

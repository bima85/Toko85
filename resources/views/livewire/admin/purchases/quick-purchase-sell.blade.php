<div>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Quick Purchase & Sell</h3>
    </div>

    <div class="card-body">
      @if (session('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
      @endif

      <div class="form-group">
        <label>Lokasi</label>
        <div class="form-inline">
          <select wire:model="location_type" class="form-control mr-2">
            <option value="store">Toko</option>
            <option value="warehouse">Gudang</option>
          </select>

          <select wire:model="location_id" class="form-control">
            @if ($location_type == 'store')
              @foreach ($stores as $s)
                <option value="{{ $s->id }}">{{ $s->nama_toko }}</option>
              @endforeach
            @else
              @foreach ($warehouses as $w)
                <option value="{{ $w->id }}">{{ $w->nama_gudang }}</option>
              @endforeach
            @endif
          </select>
        </div>
      </div>

      <table class="table table-sm table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Produk</th>
            <th>Qty Beli</th>
            <th>Harga Beli</th>
            <th>Qty Jual</th>
            <th>Harga Jual</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($items as $i => $it)
            <tr>
              <td>{{ $i + 1 }}</td>
              <td>
                <select wire:model="items.{{ $i }}.product_id" class="form-control">
                  <option value="">-- Pilih --</option>
                  @foreach ($products as $p)
                    <option value="{{ $p->id }}">
                      {{ $p->kode_produk }} - {{ $p->nama_produk }}
                    </option>
                  @endforeach
                </select>
              </td>
              <td>
                <input
                  wire:model="items.{{ $i }}.purchase_qty"
                  type="number"
                  class="form-control"
                  step="0.01"
                />
              </td>
              <td>
                <input
                  wire:model="items.{{ $i }}.purchase_price"
                  type="number"
                  class="form-control"
                  step="0.01"
                />
              </td>
              <td>
                <input
                  wire:model="items.{{ $i }}.sale_qty"
                  type="number"
                  class="form-control"
                  step="0.01"
                />
              </td>
              <td>
                <input
                  wire:model="items.{{ $i }}.sale_price"
                  type="number"
                  class="form-control"
                  step="0.01"
                />
              </td>
              <td>
                <button wire:click.prevent="removeItem({{ $i }})" class="btn btn-danger btn-sm">
                  Hapus
                </button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <div class="mt-2">
        <button wire:click.prevent="addItem" class="btn btn-secondary btn-sm">Tambah Item</button>
        <button wire:click.prevent="submit" class="btn btn-primary btn-sm">
          Proses Purchase & Sell
        </button>
      </div>
    </div>
  </div>
</div>

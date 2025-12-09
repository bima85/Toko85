<div>
  @php
    $transactionColors = [
      'in' => 'success',
      'out' => 'danger',
      'adjustment' => 'warning',
      'return' => 'info',
    ];
  @endphp

  <div class="table-responsive">
    <table class="table table-striped table-hover m-0">
      <thead class="bg-light">
        <tr>
          <th style="width: 5%">
            <a href="#" wire:click.prevent="sort('id')" class="text-dark">
              #
              @if ($sortBy === 'id')
                <i class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
              @endif
            </a>
          </th>
          <th>
            <a href="#" wire:click.prevent="sort('product_id')" class="text-dark">
              Produk
              @if ($sortBy === 'product_id')
                <i class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
              @endif
            </a>
          </th>
          <th style="width: 15%">
            <a href="#" wire:click.prevent="sort('type')" class="text-dark">
              Tipe
              @if ($sortBy === 'type')
                <i class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
              @endif
            </a>
          </th>
          <th style="width: 10%" class="text-right">
            <a href="#" wire:click.prevent="sort('qty')" class="text-dark">
              Qty
              @if ($sortBy === 'qty')
                <i class="fas fa-{{ $sortDirection === 'asc' ? 'arrow-up' : 'arrow-down' }}"></i>
              @endif
            </a>
          </th>
          <th>Batch</th>
          <th style="width: 15%">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($stockCards as $index => $card)
          <tr>
            <td>{{ $stockCards->firstItem() + $index }}</td>
            <td>
              <strong>{{ $card->product->nama_produk }}</strong>
              <br />
              <small class="text-muted">{{ $card->product->kode_produk }}</small>
            </td>
            <td>
              <span class="badge badge-{{ $transactionColors[$card->type] ?? 'secondary' }}">
                {{
                  match ($card->type) {
                    'in' => 'Masuk',
                    'out' => 'Keluar',
                    'adjustment' => 'Penyesuaian',
                    'return' => 'Retur',
                    default => ucfirst($card->type),
                  }
                }}
              </span>
            </td>
            <td class="text-right">
              <strong>{{ number_format($card->qty, 2, ',', '.') }}</strong>
            </td>
            <td>
              @if ($card->batch)
                <span class="badge badge-info">
                  {{ $card->batch->nama_tumpukan ?? 'Batch #' . $card->batch->id }}
                </span>
              @else
                <span class="text-muted">-</span>
              @endif
            </td>
            <td>
              <a href="{{ route('stock-card.show', $card->id) }}" class="btn btn-xs btn-info">
                <i class="fas fa-eye"></i>
              </a>
              <a href="{{ route('stock-card.edit', $card->id) }}" class="btn btn-xs btn-warning">
                <i class="fas fa-edit"></i>
              </a>
              <button
                type="button"
                class="btn btn-xs btn-danger"
                wire:click="deleteStockCard({{ $card->id }})"
                wire:confirm="Yakin ingin menghapus?"
              >
                <i class="fas fa-trash"></i>
              </button>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <!-- Pagination -->
  @if ($stockCards->hasPages())
    <div class="card-footer">
      {{ $stockCards->links() }}
    </div>
  @endif
</div>

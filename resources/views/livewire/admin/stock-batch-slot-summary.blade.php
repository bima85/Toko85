<!-- Info Cards Summary -->
<div class="row mb-4">
  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-lightblue">
      <span class="info-box-icon"><i class="fas fa-layer-group"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Tumpukan</span>
        <span class="info-box-number">{{ $this->tumpukanSummary->count() }}</span>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-lightgreen">
      <span class="info-box-icon"><i class="fas fa-cubes"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Stok ({{ $this->locationLabel }})</span>
        <span class="info-box-number">
          {{ rtrim(rtrim(number_format($this->totalAllTumpukan, 2), '0'), '.') }}
        </span>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-lightyellow">
      <span class="info-box-icon"><i class="fas fa-boxes"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Total Batch</span>
        <span class="info-box-number">
          @php
            $batchCount = \App\Models\StockBatch::active();
            if ($this->location) {
              $batchCount = $batchCount->where('location_type', $this->location);
            }
            echo $batchCount->count();
          @endphp
        </span>
      </div>
    </div>
  </div>

  <div class="col-12 col-sm-6 col-md-3">
    <div class="info-box bg-lightred">
      <span class="info-box-icon"><i class="fas fa-map-marker-alt"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Stok Rata-rata/Tumpukan</span>
        <span class="info-box-number">
          @php
            $avg =
              $this->tumpukanSummary->count() > 0
                ? $this->totalAllTumpukan / $this->tumpukanSummary->count()
                : 0;
            echo rtrim(rtrim(number_format($avg, 2), '0'), '.');
          @endphp
        </span>
      </div>
    </div>
  </div>
</div>

<!-- Tumpukan Summary Table - HIDDEN -->
{{--
  <div class="row mb-4">
  <div class="col-md-12">
  <div class="card card-primary card-outline">
  <div class="card-header">
  <h3 class="card-title">
  <i class="fas fa-table"></i> Ringkasan Stok Per Tumpukan
  </h3>
  </div>
  <div class="card-body">
  @if ($this->tumpukanSummary->count() > 0)
  <div class="table-responsive" style="max-height: 600px; overflow-y: auto; overflow-x: auto;">
  <table class="table table-hover" style="font-size: 15px;">
  <thead class="bg-primary text-white">
  <tr style="font-size: 16px;">
  <th class="text-center" style="width: 5%; padding: 15px;">#</th>
  <th style="width: 25%; padding: 15px;">Nama Tumpukan</th>
  <th class="text-center" style="width: 12%; padding: 15px;">Lokasi</th>
  <th class="text-center" style="width: 12%; padding: 15px;">Jumlah Batch</th>
  <th class="text-center" style="width: 12%; padding: 15px;">Total Stok</th>
  <th class="text-center" style="width: 10%; padding: 15px;">Satuan</th>
  <th class="text-center" style="width: 18%; padding: 15px;">Persentase</th>
  </tr>
  </thead>
  <tbody>
  @php
  $no = 1;
  $groupedByProduct = $this->tumpukanSummary->groupBy('product_id');
  $locations = ['store' => 'Toko', 'warehouse' => 'Gudang'];
  @endphp
  
  @foreach ($groupedByProduct as $productId => $tumpukans)
  @php
  $firstTumpukan = $tumpukans->first();
  $productTotalQty = $tumpukans->sum('total_qty');
  @endphp
  
  <!-- Product Header Row -->
  <tr class="bg-light">
  <td colspan="7" class="font-weight-bold" style="font-size: 17px; border-top: 3px solid #007bff; padding: 18px;">
  <i class="fas fa-box mr-2 text-primary" style="font-size: 18px;"></i>
  @if($firstTumpukan->product)
  <strong style="font-size: 18px;">{{ $firstTumpukan->product->nama_produk }}</strong>
  <span class="badge badge-secondary ml-2" style="font-size: 14px; padding: 8px 12px;">[{{ $firstTumpukan->product->kode_produk }}]</span>
  <span class="badge badge-info ml-2" style="font-size: 14px; padding: 8px 12px;">
  <i class="fas fa-layer-group mr-1"></i>{{ $tumpukans->count() }} Tumpukan
  </span>
  <span class="badge badge-success ml-2" style="font-size: 14px; padding: 8px 12px;">
  <i class="fas fa-cubes mr-1"></i>Total: {{ number_format($productTotalQty, 2) }} {{ $firstTumpukan->product->satuan ?? 'unit' }}
  </span>
  @else
  <span class="text-muted">Produk Tidak Ditemukan</span>
  @endif
  </td>
  </tr>
  
  <!-- Tumpukan Rows -->
  @foreach ($tumpukans as $tumpukan)
  @php
  $percentage = ($tumpukan->total_qty / $this->totalAllTumpukan) * 100;
  @endphp
  <tr>
  <td class="text-center" style="padding: 15px;">
  <span class="badge badge-dark" style="font-size: 15px; padding: 8px 12px;">{{ $no }}</span>
  </td>
  <td class="pl-4" style="padding: 15px;">
  <i class="fas fa-angle-right mr-2 text-muted" style="font-size: 16px;"></i>
  <strong style="font-size: 16px;">{{ $tumpukan->nama_tumpukan }}</strong>
  </td>
  <td class="text-center" style="padding: 15px;">
  <span class="badge badge-info" style="font-size: 14px; padding: 8px 16px;">
  {{ $locations[$tumpukan->location_type] ?? $tumpukan->location_type }}
  </span>
  </td>
  <td class="text-center" style="padding: 15px;">
  <span class="badge badge-secondary" style="font-size: 14px; padding: 8px 16px;">
  {{ $tumpukan->batch_count }} batch
  </span>
  </td>
  <td class="text-center" style="padding: 15px;">
  <span class="badge badge-success" style="font-size: 15px; padding: 8px 16px;">
  {{ number_format($tumpukan->total_qty, 2) }}
  </span>
  </td>
  <td class="text-center" style="padding: 15px;">
  <span class="badge badge-info" style="font-size: 14px; padding: 8px 16px;">{{ $tumpukan->product->satuan ?? 'unit' }}</span>
  </td>
  <td class="text-center" style="padding: 15px;">
  <div class="progress" style="height: 28px; position: relative;">
  @php
  $progressColor = 'bg-success';
  if ($percentage > 70) $progressColor = 'bg-danger';
  elseif ($percentage > 50) $progressColor = 'bg-warning';
  @endphp
  <div class="progress-bar {{ $progressColor }}" role="progressbar"
  style="width: {{ $percentage }}%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 15px;"
  aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
  {{ number_format($percentage, 1) }}%
  </div>
  </div>
  </td>
  </tr>
  @php $no++; @endphp
  @endforeach
  
  @endforeach
  </tbody>
  <tfoot class="bg-light">
  <tr class="font-weight-bold" style="font-size: 17px; border-top: 3px solid #ddd;">
  <td colspan="4" class="text-right" style="padding: 18px;">TOTAL SEMUA TUMPUKAN:</td>
  <td class="text-center" style="padding: 18px;">
  <span class="badge badge-danger" style="font-size: 16px; padding: 10px 18px;">
  {{ number_format($this->totalAllTumpukan, 2) }}
  </span>
  </td>
  <td class="text-center" style="padding: 18px;">
  @php
  $firstTumpukanSatuan = \App\Models\StockBatch::where('nama_tumpukan', $this->tumpukanSummary->first()?->nama_tumpukan ?? null)->first()?->product?->satuan ?? 'unit';
  @endphp
  <span class="badge badge-info" style="font-size: 15px; padding: 8px 16px;">{{ $firstTumpukanSatuan }}</span>
  </td>
  <td></td>
  </tr>
  </tfoot>
  </table>
  </div>
  @else
  <div class="alert alert-info text-center">
  <i class="fas fa-inbox"></i> Tidak ada data tumpukan stok
  </div>
  @endif
  </div>
  </div>
  </div>
  </div>
--}}

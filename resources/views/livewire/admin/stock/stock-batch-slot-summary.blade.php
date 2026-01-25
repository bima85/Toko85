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

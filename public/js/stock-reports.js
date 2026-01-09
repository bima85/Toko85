/**
 * Stock Reports Page - JavaScript Functions
 * Handles modal interactions and calculator functionality
 */

/**
 * Alpine.js component untuk kalkulasi stok
 * @returns {Object} Stok calculator object
 */
function stokCalculator() {
  return {
    stokAwal: 0,
    stokMasuk: 0,
    totalStok: 0,
    /**
     * Hitung total stok dari input stok awal dan stok masuk
     */
    calculateTotal() {
      const awal = parseFloat(this.$refs.stokAwal?.value || 0) || 0;
      const masuk = parseFloat(this.$refs.stokMasuk?.value || 0) || 0;
      this.stokAwal = awal;
      this.stokMasuk = masuk;
      this.totalStok = awal + masuk;

      if (window.$wire) {
        $wire.set('adjustment_total_stok', this.totalStok);
      }
    },
  };
}

/**
 * Export function ke global window agar Alpine bisa mengaksesnya
 */
window.stokCalculator = stokCalculator;

/**
 * Event listener untuk modal penyesuaian stok - tampilkan
 */
window.addEventListener('show-adjustment-modal', () => {
  $('#adjustmentModal').modal('show');
});

/**
 * Event listener untuk modal penyesuaian stok - sembunyikan
 */
window.addEventListener('hide-adjustment-modal', () => {
  $('#adjustmentModal').modal('hide');
});

/**
 * Event listeners untuk lokasi toko dan gudang
 * Pastikan hanya satu lokasi yang dipilih
 */
function setupLocationHandlers() {
  const storeLocation = document.getElementById('store_location');
  const warehouseLocation = document.getElementById('warehouse_location');

  if (storeLocation) {
    storeLocation.addEventListener('change', function () {
      if (this.checked && window.$wire) {
        $wire.set('adjustment_warehouse_id', null);
      }
    });
  }

  if (warehouseLocation) {
    warehouseLocation.addEventListener('change', function () {
      if (this.checked && window.$wire) {
        $wire.set('adjustment_store_id', null);
      }
    });
  }
}

// Setup handlers saat document ready
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupLocationHandlers);
} else {
  setupLocationHandlers();
}

// Juga setup ulang saat livewire load
window.addEventListener('livewire:load', setupLocationHandlers);

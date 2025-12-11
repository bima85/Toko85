<div>
  @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-check"></i>
      {{ session('message') }}
    </div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible">
      <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
      <i class="icon fas fa-ban"></i>
      {{ session('error') }}
    </div>
  @endif

  <!-- Content Header -->
  <div class="row mb-3">
    <div class="col-md-12">
      <h2 class="mb-0">
        <i class="fas fa-history mr-2"></i>
        Historis Transaksi
      </h2>
      <small class="text-muted">Lihat dan kelola riwayat semua transaksi dalam sistem</small>
      <hr />
    </div>
  </div>

  <!-- Filter & Search Card -->
  <div class="row mb-3">
    <div class="col-md-12">
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Filter & Pencarian
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-md-4 mb-3">
              <label><strong>Cari Transaksi</strong></label>
              <input
                type="text"
                id="searchInput"
                class="form-control form-control-sm"
                placeholder="Kode, deskripsi, atau nama user..."
              />
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
              <label><strong>Tipe Transaksi</strong></label>
              <select id="filterType" class="form-control form-control-sm">
                <option value="">-- Semua Tipe --</option>
                <option value="penjualan">Penjualan</option>
                <option value="pembelian">Pembelian</option>
                <option value="adjustment">Adjustment Stok</option>
                <option value="return">Return</option>
                <option value="other">Lainnya</option>
              </select>
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
              <label><strong>Status</strong></label>
              <select id="filterStatus" class="form-control form-control-sm">
                <option value="">-- Semua Status --</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="failed">Failed</option>
                <option value="cancelled">Cancelled</option>
              </select>
            </div>
          </div>

          <div class="row">
            <div class="col-12 col-sm-6 col-md-3 mb-3">
              <label><strong>Tanggal Dari</strong></label>
              <input type="date" id="filterDateFrom" class="form-control form-control-sm" />
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
              <label><strong>Tanggal Sampai</strong></label>
              <input type="date" id="filterDateTo" class="form-control form-control-sm" />
            </div>
            <div class="col-12 col-md-2">
              <label>&nbsp;</label>
              <button id="resetFilters" class="btn btn-secondary btn-sm btn-block w-100">
                <i class="fas fa-redo mr-1"></i>
                Reset Filter
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Transactions Table -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-outline card-info">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
          <h3 class="card-title mb-2 mb-md-0">
            <i class="fas fa-table mr-2"></i>
            Daftar Transaksi
          </h3>
          <div class="d-flex align-items-center gap-2">
            <button
              type="button"
              id="deleteSelectedBtn"
              class="btn btn-danger btn-sm"
              style="display: none"
              onclick="deleteSelectedTransactions()"
            >
              <i class="fas fa-trash mr-1"></i>
              Hapus
              <span id="selectedCount">0</span>
            </button>
            <button
              type="button"
              id="clearSelectBtn"
              class="btn btn-secondary btn-sm"
              style="display: none"
              onclick="clearAllSelections()"
            >
              <i class="fas fa-times mr-1"></i>
              Batal
            </button>

            <label class="mb-0 mr-1">Tampilkan:</label>
            <select id="pageLength" class="form-control form-control-sm" style="width: 80px">
              <option value="10">10</option>
              <option value="15" selected>15</option>
              <option value="25">25</option>
              <option value="50">50</option>
              <option value="100">100</option>
            </select>
          </div>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-sm mb-0" id="transactionsTable" style="width: 100%">
            <thead class="bg-primary text-white">
              <tr>
                <th style="width: 4%">
                  <input
                    type="checkbox"
                    wire:click="$toggle('selectAll')"
                    wire:model="selectAll"
                    title="Select all"
                  />
                </th>
                <th style="width: 12%">
                  <i class="fas fa-barcode mr-1"></i>
                  Kode
                </th>
                <th style="width: 10%">
                  <i class="fas fa-tag mr-1"></i>
                  Tipe
                </th>
                <th style="width: 15%">
                  <i class="fas fa-file-alt mr-1"></i>
                  Deskripsi
                </th>
                <th style="width: 12%">
                  <i class="fas fa-calendar mr-1"></i>
                  Tanggal
                </th>
                <th style="width: 12%" class="text-right">
                  <i class="fas fa-money-bill mr-1"></i>
                  Jumlah
                </th>
                <th style="width: 12%">
                  <i class="fas fa-user mr-1"></i>
                  User
                </th>
                <th style="width: 10%" class="text-center">
                  <i class="fas fa-info-circle mr-1"></i>
                  Status
                </th>
                <th style="width: 17%" class="text-center">
                  <i class="fas fa-cogs mr-1"></i>
                  Aksi
                </th>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Card -->
  <div class="row mt-3">
    <div class="col-12 col-md-6 col-lg-4 mb-3">
      <div class="info-box bg-light-primary">
        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Transaksi</span>
          <span class="info-box-number" id="totalAmount">Rp 0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-3">
      <div class="info-box bg-light-success">
        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Transaksi Berhasil</span>
          <span class="info-box-number" id="completedCount">0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-md-6 col-lg-4 mb-3">
      <div class="info-box bg-light-warning">
        <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Transaksi Pending</span>
          <span class="info-box-number" id="pendingCount">0</span>
        </div>
      </div>
    </div>
  </div>

  <script>
    // Wait for jQuery to be available
    if (typeof jQuery === 'undefined') {
      var loadScripts = function () {
        if (typeof jQuery !== 'undefined') {
          initTable();
        } else {
          setTimeout(loadScripts, 100);
        }
      };
      loadScripts();
    } else {
      initTable();
    }

    // Initialize table when jQuery is ready
    function initTable() {
      if (typeof $ === 'undefined' || typeof $.fn.dataTable === 'undefined') {
        setTimeout(initTable, 100);
        return;
      }

      // Destroy existing DataTable if it exists
      const tableElement = $('#transactionsTable');
      if ($.fn.dataTable.isDataTable(tableElement)) {
        tableElement.DataTable().destroy();
      }

      const table = tableElement.DataTable({
        processing: true,
        serverSide: true,
        ajax: {
          url: '{{ route('admin.transactions.data') }}',
          type: 'GET',
          data: function (d) {
            d.filterType = $('#filterType').val();
            d.filterStatus = $('#filterStatus').val();
            d.filterDateFrom = $('#filterDateFrom').val();
            d.filterDateTo = $('#filterDateTo').val();
          },
          error: function (xhr, error, thrown) {
            console.error('DataTable ajax error:', error, xhr.responseText);
          },
        },
        columns: [
          {
            data: 'id',
            render: function (data) {
              return (
                '<input type="checkbox" class="transaction-checkbox" value="' +
                data +
                '" onchange="toggleTransaction(' +
                data +
                ')" />'
              );
            },
            orderable: false,
            searchable: false,
          },
          {
            data: 'transaction_code',
            render: function (data) {
              return (
                '<span class="badge badge-info" style="font-size: 15px;"><strong>' +
                data +
                '</strong></span>'
              );
            },
          },
          {
            data: 'transaction_type',
            render: function (data) {
              var colors = {
                penjualan: 'success',
                pembelian: 'info',
                adjustment: 'warning',
                return: 'danger',
                other: 'secondary',
              };
              var color = colors[data] || 'secondary';
              return '<span class="badge badge-' + color + '">' + data + '</span>';
            },
          },
          {
            data: 'description',
            render: function (data) {
              return (
                '<small>' + (data.length > 30 ? data.substring(0, 30) + '...' : data) + '</small>'
              );
            },
          },
          { data: 'transaction_date' },
          {
            data: 'amount',
            render: function (data) {
              return '<strong class="text-primary">' + data + '</strong>';
            },
          },
          {
            data: 'user_name',
            render: function (data) {
              return (
                '<small><i class="fas fa-user-circle mr-1 text-muted"></i>' + data + '</small>'
              );
            },
          },
          { data: 'status' },
          { data: 'action', orderable: false, searchable: false },
        ],
        order: [[3, 'desc']],
        pageLength: 15,
        dom: '<"top"<"left"><"right"f>>rt<"bottom"<"left"i><"right"p>><"clear">',
        rowCallback: function (row, data, index) {
          $(row).hover(
            function () {
              $(this).css('background-color', '#e3f2fd');
            },
            function () {
              $(this).css('background-color', index % 2 === 0 ? '#ffffff' : '#f8f9fa');
            }
          );
        },
        language: {
          processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...',
          loadingRecords: 'Memuat...',
          lengthMenu: 'Tampilkan _MENU_',
          info: 'Menampilkan _START_ ke _END_ dari _TOTAL_ transaksi',
          emptyTable: '<i class="fas fa-inbox mr-2"></i>Tidak ada data transaksi',
          zeroRecords: '<i class="fas fa-search mr-2"></i>Transaksi tidak ditemukan',
          search: 'Cari dalam tabel:',
        },
      });

      $('#searchInput').on('keyup', function () {
        table.search($(this).val()).draw();
      });

      $('#filterType, #filterStatus, #filterDateFrom, #filterDateTo').on('change', function () {
        table.draw();
      });

      $('#pageLength').on('change', function () {
        table.page.len($(this).val()).draw();
      });

      $('#resetFilters').on('click', function () {
        $('#searchInput, #filterType, #filterStatus, #filterDateFrom, #filterDateTo').val('');
        table.search('').draw();
      });

      table.on('draw', updateStats);
      updateStats();

      // Initialize selectedTransactionIds globally
      window.selectedTransactionIds = [];

      // Livewire event listener untuk reload table setelah bulk delete
      Livewire.on('reloadTransactionsTable', function () {
        console.log('reloadTransactionsTable event received, reloading...');
        table.ajax.reload();
      });

      // Handle select all checkbox
      const selectAllCheckbox = document.querySelector('[wire\\:model="selectAll"]');
      if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function (e) {
          e.preventDefault();
          e.stopPropagation();

          if (this.checked) {
            // Collect all transaction IDs from current filtered data
            console.log('Select all clicked, collecting IDs...');

            // Fetch all matching IDs from server based on current filters
            $.ajax({
              url: '{{ route('admin.transactions.data') }}',
              type: 'GET',
              data: {
                draw: 1,
                start: 0,
                length: -1, // Get all records
                filterType: $('#filterType').val(),
                filterStatus: $('#filterStatus').val(),
                filterDateFrom: $('#filterDateFrom').val(),
                filterDateTo: $('#filterDateTo').val(),
                search: { value: $('#searchInput').val() }
              },
              dataType: 'json',
              success: function (response) {
                const ids = response.data.map((item) => parseInt(item.id));
                console.log('Fetched ' + ids.length + ' IDs from server:', ids);

                // Store IDs in global variable
                window.selectedTransactionIds = ids;

                // Check all visible checkboxes
                $('#transactionsTable tbody .transaction-checkbox').each(function () {
                  $(this).prop('checked', true);
                });

                // Update button visibility
                updateDeleteButtons();
              },
              error: function (xhr) {
                console.error('Failed to fetch transaction IDs', xhr);
                selectAllCheckbox.checked = false;
              }
            });
          } else {
            // Uncheck all
            window.selectedTransactionIds = [];
            $('#transactionsTable tbody .transaction-checkbox').prop('checked', false);
            selectAllCheckbox.prop('checked', false);
            updateDeleteButtons();
          }
        });
      }

      // Update selected transaction count and button visibility
      function updateDeleteButtons() {
        const selectedCount = window.selectedTransactionIds.length;
        $('#selectedCount').text(selectedCount);

        if (selectedCount > 0) {
          $('#deleteSelectedBtn, #clearSelectBtn').show();
        } else {
          $('#deleteSelectedBtn, #clearSelectBtn').hide();
        }
      }

      // Handle individual checkbox clicks
      $(document).on('change', '.transaction-checkbox', function () {
        const id = parseInt($(this).val());

        if ($(this).is(':checked')) {
          if (!window.selectedTransactionIds.includes(id)) {
            window.selectedTransactionIds.push(id);
          }
        } else {
          window.selectedTransactionIds = window.selectedTransactionIds.filter(x => x !== id);
          // Uncheck select all if any item is unchecked
          if (selectAllCheckbox) {
            selectAllCheckbox.prop('checked', false);
          }
        }

        updateDeleteButtons();
      });

      // Delete selected transactions
      window.deleteSelectedTransactions = function () {
        if (!window.selectedTransactionIds || window.selectedTransactionIds.length === 0) {
          alert('Pilih transaksi untuk dihapus');
          return;
        }

        const count = window.selectedTransactionIds.length;
        if (!confirm('Hapus ' + count + ' transaksi? Tindakan ini tidak dapat dibatalkan.')) {
          return;
        }

        // Call Livewire deleteSelected method with collected IDs
        @this.call('deleteSelected', window.selectedTransactionIds);
      };

      // Clear all selections
      window.clearAllSelections = function () {
        window.selectedTransactionIds = [];
        $('#transactionsTable tbody .transaction-checkbox').prop('checked', false);
        if (selectAllCheckbox) {
          selectAllCheckbox.prop('checked', false);
        }
        updateDeleteButtons();
      };

    }

    function updateStats() {
      $.ajax({
        url: '{{ route('admin.transactions.data') }}',
        data: {
          draw: 1,
          start: 0,
          length: -1,
          filterType: $('#filterType').val(),
          filterStatus: $('#filterStatus').val(),
          filterDateFrom: $('#filterDateFrom').val(),
          filterDateTo: $('#filterDateTo').val(),
        },
        success: function (data) {
          let total = 0,
            completed = 0,
            pending = 0;
          data.data.forEach((row) => {
            let amt = parseInt($('<div>').html(row.amount).text().replace(/[^\d]/g, '')) || 0;
            total += amt;
            if (row.status.includes('success')) completed++;
            if (row.status.includes('warning')) pending++;
          });
          $('#totalAmount').text('Rp ' + total.toLocaleString('id-ID'));
          $('#completedCount').text(completed);
          $('#pendingCount').text(pending);
        },
      });
    }

    // Safe initialization when document is ready
    if (document.readyState === 'loading') {
      document.addEventListener('DOMContentLoaded', function () {
        setTimeout(initTable, 500);
      });
    } else {
      setTimeout(initTable, 500);
    }
  </script>
</div>

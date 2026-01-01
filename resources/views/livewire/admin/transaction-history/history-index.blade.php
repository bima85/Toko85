<div>
  @push('styles')
    <!-- DataTables CSS -->
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css"
    />
    <link
      rel="stylesheet"
      href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css"
    />

    <style>
      .info-box.bg-light-primary {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%) !important;
      }
      .info-box.bg-light-success {
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%) !important;
      }
      .info-box.bg-light-warning {
        background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%) !important;
      }
      .info-box.bg-light-danger {
        background: linear-gradient(135deg, #ffebee 0%, #ffcdd2 100%) !important;
      }
      .info-box .info-box-icon {
        width: 70px;
        font-size: 1.8rem;
      }
      .transaction-row:hover {
        background-color: #e3f2fd !important;
      }
      .badge-penjualan {
        background-color: #28a745;
      }
      .badge-pembelian {
        background-color: #17a2b8;
      }
      .badge-adjustment {
        background-color: #ffc107;
        color: #212529;
      }
      .badge-return {
        background-color: #dc3545;
      }
      .status-completed {
        color: #28a745;
      }
      .status-pending {
        color: #ffc107;
      }
      .status-cancelled {
        color: #dc3545;
      }
    </style>
  @endpush

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

  <!-- Statistics Cards -->
  <div class="row mb-3">
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
      <div class="info-box bg-light-primary">
        <span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Total Transaksi</span>
          <span class="info-box-number" id="totalAmount">Rp 0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
      <div class="info-box bg-light-success">
        <span class="info-box-icon"><i class="fas fa-check-circle"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Transaksi Selesai</span>
          <span class="info-box-number" id="completedCount">0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
      <div class="info-box bg-light-warning">
        <span class="info-box-icon"><i class="fas fa-hourglass-half"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Transaksi Pending</span>
          <span class="info-box-number" id="pendingCount">0</span>
        </div>
      </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3 mb-3">
      <div class="info-box bg-light-danger">
        <span class="info-box-icon"><i class="fas fa-times-circle"></i></span>
        <div class="info-box-content">
          <span class="info-box-text">Transaksi Batal</span>
          <span class="info-box-number" id="cancelledCount">0</span>
        </div>
      </div>
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
          <div class="card-tools">
            <button type="button" class="btn btn-tool" data-card-widget="collapse">
              <i class="fas fa-minus"></i>
            </button>
          </div>
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
            <div class="col-12 col-sm-6 col-md-2 mb-3">
              <label><strong>Tipe Transaksi</strong></label>
              <select id="filterType" class="form-control form-control-sm">
                <option value="">-- Semua Tipe --</option>
                <option value="penjualan">Penjualan</option>
                <option value="pembelian">Pembelian</option>
                <option value="adjustment">Adjustment</option>
                <option value="return">Return</option>
              </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2 mb-3">
              <label><strong>Status</strong></label>
              <select id="filterStatus" class="form-control form-control-sm">
                <option value="">-- Semua Status --</option>
                <option value="completed">Selesai</option>
                <option value="pending">Pending</option>
                <option value="cancelled">Batal</option>
              </select>
            </div>
            <div class="col-12 col-sm-6 col-md-2 mb-3">
              <label><strong>Tanggal Dari</strong></label>
              <input type="date" id="filterDateFrom" class="form-control form-control-sm" />
            </div>
            <div class="col-12 col-sm-6 col-md-2 mb-3">
              <label><strong>Tanggal Sampai</strong></label>
              <input type="date" id="filterDateTo" class="form-control form-control-sm" />
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <button id="resetFilters" class="btn btn-secondary btn-sm">
                <i class="fas fa-redo mr-1"></i>
                Reset Filter
              </button>
              <button id="exportExcel" class="btn btn-success btn-sm ml-2">
                <i class="fas fa-file-excel mr-1"></i>
                Export Excel
              </button>
              <button id="exportPdf" class="btn btn-danger btn-sm ml-2">
                <i class="fas fa-file-pdf mr-1"></i>
                Export PDF
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
              class="btn btn-danger btn-sm mr-2"
              style="display: none"
              onclick="deleteSelectedTransactions()"
            >
              <i class="fas fa-trash mr-1"></i>
              Hapus
              <span class="badge badge-light ml-1" id="selectedCount">0</span>
            </button>
            <button
              type="button"
              id="clearSelectBtn"
              class="btn btn-secondary btn-sm mr-2"
              style="display: none"
              onclick="clearAllSelections()"
            >
              <i class="fas fa-times mr-1"></i>
              Batal Pilih
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
          <table class="table table-sm table-hover mb-0" id="transactionsTable" style="width: 100%">
            <thead class="bg-primary text-white">
              <tr>
                <th style="width: 4%">
                  <input
                    type="checkbox"
                    id="selectAllCheckbox"
                    wire:click="$toggle('selectAll')"
                    wire:model="selectAll"
                    title="Pilih semua"
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
                <th style="width: 18%">
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
                <th style="width: 10%">
                  <i class="fas fa-user mr-1"></i>
                  User
                </th>
                <th style="width: 8%" class="text-center">
                  <i class="fas fa-info-circle mr-1"></i>
                  Status
                </th>
                <th style="width: 14%" class="text-center">
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

  <!-- Detail Modal -->
  <div
    class="modal fade"
    id="detailModal"
    tabindex="-1"
    aria-labelledby="detailModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="detailModalLabel">
            <i class="fas fa-info-circle mr-2"></i>
            Detail Transaksi
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body" id="detailModalBody">
          <div class="text-center">
            <i class="fas fa-spinner fa-spin fa-2x"></i>
            <p>Memuat data...</p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>
            Tutup
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div
    class="modal fade"
    id="deleteModal"
    tabindex="-1"
    aria-labelledby="deleteModalLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteModalLabel">
            <i class="fas fa-exclamation-triangle mr-2"></i>
            Konfirmasi Hapus
          </h5>
          <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p id="deleteModalText">Apakah Anda yakin ingin menghapus transaksi ini?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            <i class="fas fa-times mr-1"></i>
            Batal
          </button>
          <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
            <i class="fas fa-trash mr-1"></i>
            Hapus
          </button>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <script>
      // Global variables
      window.selectedTransactionIds = [];
      var transactionTable = null;

      // Wait for jQuery and DataTables to be available
      function waitForDependencies(callback) {
        if (typeof jQuery !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
          callback();
        } else {
          setTimeout(function() { waitForDependencies(callback); }, 100);
        }
      }

      // Initialize table
      function initTable() {
        if (transactionTable !== null) {
          return; // Already initialized
        }

        const tableElement = $('#transactionsTable');
        if ($.fn.dataTable.isDataTable(tableElement)) {
          tableElement.DataTable().destroy();
        }

        transactionTable = tableElement.DataTable({
          processing: true,
          serverSide: true,
          ajax: {
            url: '{{ route('admin.transactions.data') }}',
            type: 'GET',
            data: function(d) {
              d.filterType = $('#filterType').val();
              d.filterStatus = $('#filterStatus').val();
              d.filterDateFrom = $('#filterDateFrom').val();
              d.filterDateTo = $('#filterDateTo').val();
            },
            error: function(xhr, error, thrown) {
              console.error('DataTable ajax error:', error, xhr.responseText);
            }
          },
          columns: [
            {
              data: 'id',
              render: function(data) {
                var checked = window.selectedTransactionIds.includes(parseInt(data)) ? 'checked' : '';
                return '<input type="checkbox" class="transaction-checkbox" value="' + data + '" ' + checked + ' />';
              },
              orderable: false,
              searchable: false
            },
            {
              data: 'transaction_code',
              render: function(data) {
                return '<span class="badge badge-info font-weight-bold" style="font-size: 12px;">' + data + '</span>';
              }
            },
            {
              data: 'transaction_type',
              render: function(data) {
                var colors = {
                  'penjualan': 'success',
                  'pembelian': 'info',
                  'adjustment': 'warning',
                  'return': 'danger'
                };
                var labels = {
                  'penjualan': 'Penjualan',
                  'pembelian': 'Pembelian',
                  'adjustment': 'Adjustment',
                  'return': 'Return'
                };
                var color = colors[data] || 'secondary';
                var label = labels[data] || data;
                return '<span class="badge badge-' + color + '">' + label + '</span>';
              }
            },
            {
              data: 'description',
              render: function(data) {
                if (!data) return '-';
                var truncated = data.length > 40 ? data.substring(0, 40) + '...' : data;
                return '<small title="' + data + '">' + truncated + '</small>';
              }
            },
            {
              data: 'transaction_date',
              render: function(data) {
                return '<small><i class="far fa-calendar-alt mr-1 text-muted"></i>' + data + '</small>';
              }
            },
            {
              data: 'total_amount',
              render: function(data) {
                return '<strong class="text-primary">' + data + '</strong>';
              },
              className: 'text-right'
            },
            {
              data: 'user_name',
              render: function(data) {
                return '<small><i class="fas fa-user-circle mr-1 text-muted"></i>' + data + '</small>';
              }
            },
            {
              data: 'status',
              render: function(data) {
                var colors = {
                  'completed': 'success',
                  'pending': 'warning',
                  'cancelled': 'danger'
                };
                var icons = {
                  'completed': 'check-circle',
                  'pending': 'hourglass-half',
                  'cancelled': 'times-circle'
                };
                var labels = {
                  'completed': 'Selesai',
                  'pending': 'Pending',
                  'cancelled': 'Batal'
                };
                var color = colors[data] || 'secondary';
                var icon = icons[data] || 'question-circle';
                var label = labels[data] || data;
                return '<span class="badge badge-' + color + '"><i class="fas fa-' + icon + ' mr-1"></i>' + label + '</span>';
              },
              className: 'text-center'
            },
            {
              data: 'id',
              render: function(data, type, row) {
                return '<div class="btn-group btn-group-sm">' +
                  '<button class="btn btn-info btn-sm" onclick="showDetail(' + data + ')" title="Lihat Detail">' +
                  '<i class="fas fa-eye"></i></button>' +
                  '<button class="btn btn-danger btn-sm" onclick="confirmDelete(' + data + ')" title="Hapus">' +
                  '<i class="fas fa-trash"></i></button>' +
                  '</div>';
              },
              orderable: false,
              searchable: false,
              className: 'text-center'
            }
          ],
          order: [[4, 'desc']],
          pageLength: 15,
          dom: '<"top">rt<"bottom"<"d-flex justify-content-between align-items-center"ip>><"clear">',
          language: {
            processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...',
            loadingRecords: 'Memuat...',
            lengthMenu: 'Tampilkan _MENU_',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ transaksi',
            infoEmpty: 'Tidak ada data',
            emptyTable: '<i class="fas fa-inbox mr-2"></i>Tidak ada data transaksi',
            zeroRecords: '<i class="fas fa-search mr-2"></i>Transaksi tidak ditemukan',
            paginate: {
              first: '<i class="fas fa-angle-double-left"></i>',
              previous: '<i class="fas fa-angle-left"></i>',
              next: '<i class="fas fa-angle-right"></i>',
              last: '<i class="fas fa-angle-double-right"></i>'
            }
          },
          drawCallback: function() {
            updateCheckboxStates();
          }
        });

        // Search input
        $('#searchInput').on('keyup', function() {
          transactionTable.search($(this).val()).draw();
        });

        // Filter change events
        $('#filterType, #filterStatus, #filterDateFrom, #filterDateTo').on('change', function() {
          transactionTable.draw();
          updateStats();
        });

        // Page length change
        $('#pageLength').on('change', function() {
          transactionTable.page.len($(this).val()).draw();
        });

        // Reset filters
        $('#resetFilters').on('click', function() {
          $('#searchInput').val('');
          $('#filterType').val('');
          $('#filterStatus').val('');
          $('#filterDateFrom').val('');
          $('#filterDateTo').val('');
          transactionTable.search('').draw();
          updateStats();
        });

        // Export Excel button
        $('#exportExcel').on('click', function() {
          var url = '{{ route('admin.transactions.export') }}';
          var params = [];
          if ($('#filterType').val()) params.push('filterType=' + encodeURIComponent($('#filterType').val()));
          if ($('#filterStatus').val()) params.push('filterStatus=' + encodeURIComponent($('#filterStatus').val()));
          if ($('#filterDateFrom').val()) params.push('filterDateFrom=' + encodeURIComponent($('#filterDateFrom').val()));
          if ($('#filterDateTo').val()) params.push('filterDateTo=' + encodeURIComponent($('#filterDateTo').val()));
          var searchVal = $('#searchInput').val();
          if (searchVal) params.push('search=' + encodeURIComponent(searchVal));
          if (params.length > 0) url += '?' + params.join('&');
          // Navigate to URL to trigger download
          window.location = url;
        });

        // Handle select all checkbox
        $('#selectAllCheckbox').on('change', function() {
          if ($(this).is(':checked')) {
            // Fetch all IDs from server
            $.ajax({
              url: '{{ route('admin.transactions.data') }}',
              type: 'GET',
              data: {
                draw: 1,
                start: 0,
                length: -1,
                filterType: $('#filterType').val(),
                filterStatus: $('#filterStatus').val(),
                filterDateFrom: $('#filterDateFrom').val(),
                filterDateTo: $('#filterDateTo').val(),
                search: { value: $('#searchInput').val() }
              },
              success: function(response) {
                window.selectedTransactionIds = response.data.map(function(item) {
                  return parseInt(item.id);
                });
                $('.transaction-checkbox').prop('checked', true);
                updateDeleteButtons();
              }
            });
          } else {
            window.selectedTransactionIds = [];
            $('.transaction-checkbox').prop('checked', false);
            updateDeleteButtons();
          }
        });

        // Handle individual checkbox clicks
        $(document).on('change', '.transaction-checkbox', function() {
          var id = parseInt($(this).val());
          if ($(this).is(':checked')) {
            if (!window.selectedTransactionIds.includes(id)) {
              window.selectedTransactionIds.push(id);
            }
          } else {
            window.selectedTransactionIds = window.selectedTransactionIds.filter(function(x) {
              return x !== id;
            });
            $('#selectAllCheckbox').prop('checked', false);
          }
          updateDeleteButtons();
        });

        // Livewire event listeners
        Livewire.on('reloadTransactionsTable', function() {
          transactionTable.ajax.reload();
          window.selectedTransactionIds = [];
          updateDeleteButtons();
        });

        Livewire.on('clearSelection', function() {
          window.selectedTransactionIds = [];
          $('.transaction-checkbox').prop('checked', false);
          $('#selectAllCheckbox').prop('checked', false);
          updateDeleteButtons();
        });

        // Initial stats update
        updateStats();
      }

      function updateCheckboxStates() {
        $('.transaction-checkbox').each(function() {
          var id = parseInt($(this).val());
          $(this).prop('checked', window.selectedTransactionIds.includes(id));
        });
      }

      function updateDeleteButtons() {
        var count = window.selectedTransactionIds.length;
        $('#selectedCount').text(count);
        if (count > 0) {
          $('#deleteSelectedBtn, #clearSelectBtn').show();
        } else {
          $('#deleteSelectedBtn, #clearSelectBtn').hide();
        }
      }

      function updateStats() {
        $.ajax({
          url: '{{ route('admin.transactions.stats') }}',
          type: 'GET',
          data: {
            filterType: $('#filterType').val(),
            filterStatus: $('#filterStatus').val(),
            filterDateFrom: $('#filterDateFrom').val(),
            filterDateTo: $('#filterDateTo').val()
          },
          success: function(response) {
            $('#totalAmount').text(response.total_amount_formatted || 'Rp 0');
            $('#completedCount').text(response.completed_count || 0);
            $('#pendingCount').text(response.pending_count || 0);
            $('#cancelledCount').text(response.cancelled_count || 0);
          },
          error: function(xhr) {
            console.error('Failed to fetch stats:', xhr);
          }
        });
      }

      function showDetail(id) {
        $('#detailModalBody').html('<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p>Memuat data...</p></div>');
        $('#detailModal').modal('show');

        // Fetch transaction detail using the detail endpoint
        $.ajax({
          url: '/admin/transactions/' + id + '/detail',
          type: 'GET',
          success: function(response) {
            if (response.success && response.data) {
              var transaction = response.data;
              var statusColors = {
                'completed': 'success',
                'pending': 'warning',
                'cancelled': 'danger'
              };
              var typeColors = {
                'penjualan': 'success',
                'pembelian': 'info',
                'adjustment': 'warning',
                'return': 'danger'
              };
              var statusLabels = {
                'completed': 'Selesai',
                'pending': 'Pending',
                'cancelled': 'Batal'
              };
              var typeLabels = {
                'penjualan': 'Penjualan',
                'pembelian': 'Pembelian',
                'adjustment': 'Adjustment',
                'return': 'Return'
              };

              var html = '<div class="row">' +
                '<div class="col-md-6">' +
                '<table class="table table-sm table-borderless">' +
                '<tr><td width="40%"><strong>Kode Transaksi</strong></td><td>: <span class="badge badge-info font-weight-bold" style="font-size: 13px;">' + transaction.transaction_code + '</span></td></tr>' +
                '<tr><td><strong>Tipe</strong></td><td>: <span class="badge badge-' + (typeColors[transaction.transaction_type] || 'secondary') + '">' + (typeLabels[transaction.transaction_type] || transaction.transaction_type) + '</span></td></tr>' +
                '<tr><td><strong>Tanggal</strong></td><td>: <i class="far fa-calendar-alt mr-1"></i>' + transaction.transaction_date_formatted + '</td></tr>' +
                '<tr><td><strong>Status</strong></td><td>: <span class="badge badge-' + (statusColors[transaction.status] || 'secondary') + '">' + (statusLabels[transaction.status] || transaction.status) + '</span></td></tr>' +
                '</table></div>' +
                '<div class="col-md-6">' +
                '<table class="table table-sm table-borderless">' +
                '<tr><td width="40%"><strong>Total</strong></td><td>: <span class="text-primary font-weight-bold" style="font-size: 16px;">' + transaction.amount_formatted + '</span></td></tr>' +
                '<tr><td><strong>User</strong></td><td>: <i class="fas fa-user-circle mr-1"></i>' + transaction.user_name + '</td></tr>' +
                '<tr><td><strong>Metode Bayar</strong></td><td>: ' + (transaction.payment_method || '-') + '</td></tr>' +
                '</table></div></div>';

              // Add description
              html += '<hr><div class="row"><div class="col-12">' +
                '<strong><i class="fas fa-file-alt mr-1"></i>Deskripsi:</strong><p class="mt-2">' + (transaction.description || '-') + '</p>' +
                '</div></div>';

              // Add notes if exists
              if (transaction.notes) {
                html += '<div class="row"><div class="col-12">' +
                  '<strong><i class="fas fa-sticky-note mr-1"></i>Catatan:</strong><p class="mt-2">' + transaction.notes + '</p>' +
                  '</div></div>';
              }

              // Add sale/purchase items if available
              if (transaction.additional_info) {
                if (transaction.additional_info.sale && transaction.additional_info.sale.items) {
                  html += '<hr><div class="row"><div class="col-12">' +
                    '<strong><i class="fas fa-shopping-cart mr-1"></i>Detail Penjualan</strong>' +
                    '<p class="mb-2"><small>Pelanggan: ' + transaction.additional_info.sale.customer + '</small></p>' +
                    '<div class="table-responsive"><table class="table table-sm table-bordered">' +
                    '<thead class="bg-light"><tr><th>Produk</th><th class="text-center">Qty</th><th>Satuan</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead><tbody>';
                  transaction.additional_info.sale.items.forEach(function(item) {
                    html += '<tr><td>' + item.product + '</td><td class="text-center">' + item.qty + '</td><td>' + item.unit + '</td><td class="text-right">' + item.price + '</td><td class="text-right">' + item.subtotal + '</td></tr>';
                  });
                  html += '</tbody></table></div></div></div>';
                }

                if (transaction.additional_info.purchase && transaction.additional_info.purchase.items) {
                  html += '<hr><div class="row"><div class="col-12">' +
                    '<strong><i class="fas fa-truck mr-1"></i>Detail Pembelian</strong>' +
                    '<p class="mb-2"><small>Supplier: ' + transaction.additional_info.purchase.supplier + '</small></p>' +
                    '<div class="table-responsive"><table class="table table-sm table-bordered">' +
                    '<thead class="bg-light"><tr><th>Produk</th><th class="text-center">Qty</th><th>Satuan</th><th class="text-right">Harga</th><th class="text-right">Subtotal</th></tr></thead><tbody>';
                  transaction.additional_info.purchase.items.forEach(function(item) {
                    html += '<tr><td>' + item.product + '</td><td class="text-center">' + item.qty + '</td><td>' + item.unit + '</td><td class="text-right">' + item.price + '</td><td class="text-right">' + item.subtotal + '</td></tr>';
                  });
                  html += '</tbody></table></div></div></div>';
                }
              }

              $('#detailModalBody').html(html);
            } else {
              $('#detailModalBody').html('<div class="alert alert-warning"><i class="fas fa-exclamation-triangle mr-2"></i>Data tidak ditemukan</div>');
            }
          },
          error: function() {
            $('#detailModalBody').html('<div class="alert alert-danger"><i class="fas fa-times-circle mr-2"></i>Gagal memuat data</div>');
          }
        });
      }

      function confirmDelete(id) {
        window.deleteTransactionId = id;
        $('#deleteModalText').text('Apakah Anda yakin ingin menghapus transaksi ini?');
        $('#confirmDeleteBtn').off('click').on('click', function() {
          @this.call('deleteSelected', [id]);
          $('#deleteModal').modal('hide');
        });
        $('#deleteModal').modal('show');
      }

      function deleteSelectedTransactions() {
        if (window.selectedTransactionIds.length === 0) {
          alert('Pilih transaksi yang ingin dihapus');
          return;
        }

        var count = window.selectedTransactionIds.length;
        $('#deleteModalText').text('Apakah Anda yakin ingin menghapus ' + count + ' transaksi?');
        $('#confirmDeleteBtn').off('click').on('click', function() {
          @this.call('deleteSelected', window.selectedTransactionIds);
          $('#deleteModal').modal('hide');
        });
        $('#deleteModal').modal('show');
      }

      function clearAllSelections() {
        window.selectedTransactionIds = [];
        $('.transaction-checkbox').prop('checked', false);
        $('#selectAllCheckbox').prop('checked', false);
        updateDeleteButtons();
      }

      // Initialize when document is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
          waitForDependencies(initTable);
        });
      } else {
        waitForDependencies(initTable);
      }
    </script>
  @endpush
</div>

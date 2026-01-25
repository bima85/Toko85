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
      #profitTable thead th {
        background-color: #f8f9fa;
        font-weight: 600;
        vertical-align: middle;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
      }
      #profitTable tfoot th {
        background-color: #e9ecef;
        font-weight: 700;
        border-top: 2px solid #dee2e6;
        font-size: 0.95rem;
        white-space: nowrap;
      }
      #profitTable tbody td {
        vertical-align: middle;
      }
      /* Prevent wrapping on small important columns and price columns */
      .nowrap {
        white-space: nowrap !important;
      }
      .price-col {
        white-space: nowrap !important;
      }
      /* Improve small-screen readability */
      @media (max-width: 768px) {
        #profitTable thead th,
        #profitTable tbody td {
          font-size: 12px;
        }
      }
    </style>
  @endpush

  <!-- Page Header -->
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">
            <i class="fas fa-chart-line mr-2"></i>
            Analisis Margin Profit
          </h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Margin Profit</li>
          </ol>
        </div>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="row">
    <div class="col-lg-3 col-6">
      <div class="info-box bg-light-primary">
        <span class="info-box-icon">
          <i class="fas fa-shopping-cart text-primary"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text"><strong>Total Penjualan</strong></span>
          <span class="info-box-number" id="totalPenjualan">Rp 0</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="info-box bg-light-warning">
        <span class="info-box-icon">
          <i class="fas fa-dollar-sign text-warning"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text"><strong>Total Modal</strong></span>
          <span class="info-box-number" id="totalModal">Rp 0</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="info-box bg-light-success">
        <span class="info-box-icon">
          <i class="fas fa-hand-holding-usd text-success"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text"><strong>Total Profit</strong></span>
          <span class="info-box-number" id="totalProfit">Rp 0</span>
        </div>
      </div>
    </div>
    <div class="col-lg-3 col-6">
      <div class="info-box bg-light-danger">
        <span class="info-box-icon">
          <i class="fas fa-percentage text-danger"></i>
        </span>
        <div class="info-box-content">
          <span class="info-box-text"><strong>Rata-rata Margin</strong></span>
          <span class="info-box-number" id="avgMargin">0%</span>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-outline card-primary">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-filter mr-2"></i>
            Filter Data
          </h3>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-12 col-sm-6 col-md-3 mb-3">
              <label><strong>Tanggal Dari</strong></label>
              <input type="date" id="filterDateFrom" class="form-control form-control-sm" />
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
              <label><strong>Tanggal Sampai</strong></label>
              <input type="date" id="filterDateTo" class="form-control form-control-sm" />
            </div>
            <div class="col-12 col-sm-6 col-md-3 mb-3">
              <label><strong>Customer</strong></label>
              <select id="filterCustomer" class="form-control form-control-sm">
                <option value="">-- Semua Customer --</option>
              </select>
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
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Data Table -->
  <div class="row">
    <div class="col-md-12">
      <div class="card card-outline card-info">
        <div class="card-header">
          <h3 class="card-title">
            <i class="fas fa-table mr-2"></i>
            Data Margin Profit
          </h3>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <div id="filterAlert" class="alert d-none" role="alert">
              <span id="filterAlertText"></span>
              <button id="filterAlertAction" class="btn btn-sm btn-outline-primary ml-2 d-none">
                Tampilkan semua transaksi customer ini
              </button>
            </div>

            <table
              id="profitTable"
              class="table table-bordered table-hover table-sm"
              style="width: 100%"
            >
              <thead class="bg-light">
                <tr>
                  <th width="5%">No</th>
                  <th>Invoice</th>
                  <th>Tanggal</th>
                  <th>Customer</th>
                  <th>Produk</th>
                  <th>Qty</th>
                  <th>Satuan</th>
                  <th>Harga Beli</th>
                  <th>Harga Jual</th>
                  <th>Total Beli</th>
                  <th>Total Jual</th>
                  <th>Profit</th>
                  <th>Margin %</th>
                  <th>User</th>
                </tr>
              </thead>
              <tbody></tbody>
              <tfoot class="bg-light">
                <tr>
                  <th colspan="9" class="text-right"><strong>GRAND TOTAL:</strong></th>
                  <th class="text-right" id="footerTotalBeli">Rp 0</th>
                  <th class="text-right" id="footerTotalJual">Rp 0</th>
                  <th class="text-right" id="footerProfit">Rp 0</th>
                  <th class="text-center" id="footerMargin">0%</th>
                  <th></th>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  @push('scripts')
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
      var profitTable = null;

      function waitForDependencies(callback) {
        if (typeof jQuery !== 'undefined' && typeof $.fn.dataTable !== 'undefined') {
          callback();
        } else {
          setTimeout(function () {
            waitForDependencies(callback);
          }, 100);
        }
      }

      function initTable() {
        if (profitTable !== null) {
          return;
        }

        const tableElement = $('#profitTable');
        if ($.fn.dataTable.isDataTable(tableElement)) {
          tableElement.DataTable().destroy();
        }

        profitTable = tableElement.DataTable({
          processing: true,
          serverSide: true,
          responsive: true,
          autoWidth: false,
          ajax: {
            url: '{{ route('admin.profit-margin.data') }}',
            type: 'GET',
            data: function (d) {
              d.filterDateFrom = $('#filterDateFrom').val();
              d.filterDateTo = $('#filterDateTo').val();
              d.filterCustomer = $('#filterCustomer').val();
            },
            error: function (xhr, error, thrown) {
              console.error('DataTable ajax error:', error, xhr.responseText);
            },
          },
          columns: [
            {
              data: 'DT_RowIndex',
              orderable: false,
              searchable: false,
              className: 'text-center nowrap',
            },
            { data: 'no_invoice', className: 'nowrap' },
            { data: 'tanggal', className: 'nowrap text-center' },
            { data: 'customer', className: 'nowrap' },
            { data: 'product' },
            { data: 'qty', className: 'text-center nowrap' },
            { data: 'unit', className: 'text-center nowrap' },
            { data: 'harga_beli_formatted', className: 'text-right price-col' },
            { data: 'harga_jual_formatted', className: 'text-right price-col' },
            { data: 'total_beli_formatted', className: 'text-right price-col' },
            { data: 'total_jual_formatted', className: 'text-right price-col' },
            { data: 'profit_formatted', className: 'text-right price-col' },
            { data: 'margin_persen_formatted', className: 'text-center nowrap' },
            { data: 'user', className: 'nowrap' },
          ],
          columnDefs: [
            { targets: [7, 8, 9, 10, 11], orderable: false },
            { targets: [0, 5, 6, 12], width: '6%' },
            { targets: [1], responsivePriority: 1 },
            { targets: [2], responsivePriority: 2 },
            { targets: [3], responsivePriority: 3 },
            { targets: [4], responsivePriority: 4 },
          ],
          createdRow: function (row, data, dataIndex) {
            // product column tooltip
            $('td', row).eq(4).attr('title', data.product).attr('data-toggle', 'tooltip');
          },
          order: [[2, 'desc']],
          pageLength: 15,
          footerCallback: function (row, data, start, end, display) {
            var api = this.api();

            // Calculate totals
            var totalBeli = 0;
            var totalJual = 0;
            var totalProfit = 0;

            api.rows({ search: 'applied' }).every(function () {
              var rowData = this.data();
              totalBeli += parseFloat(rowData.total_beli) || 0;
              totalJual += parseFloat(rowData.total_jual) || 0;
              totalProfit += parseFloat(rowData.profit) || 0;
            });

            var avgMargin = totalJual > 0 ? (totalProfit / totalJual) * 100 : 0;

            // Update footer
            $('#footerTotalBeli').html(
              '<strong>Rp ' + totalBeli.toLocaleString('id-ID') + '</strong>'
            );
            $('#footerTotalJual').html(
              '<strong>Rp ' + totalJual.toLocaleString('id-ID') + '</strong>'
            );

            var profitClass = totalProfit >= 0 ? 'text-success' : 'text-danger';
            $('#footerProfit').html(
              '<strong class="' +
                profitClass +
                '">Rp ' +
                totalProfit.toLocaleString('id-ID') +
                '</strong>'
            );

            var marginClass =
              avgMargin >= 30
                ? 'badge-success'
                : avgMargin >= 15
                  ? 'badge-warning'
                  : 'badge-danger';
            $('#footerMargin').html(
              '<span class="badge ' + marginClass + '">' + avgMargin.toFixed(2) + '%</span>'
            );
          },
          dom: '<"top">rt<"bottom"<"d-flex justify-content-between align-items-center"ip>><"clear">',
          language: {
            processing: '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...',
            loadingRecords: 'Memuat...',
            lengthMenu: 'Tampilkan _MENU_',
            info: 'Menampilkan _START_ - _END_ dari _TOTAL_ data',
            infoEmpty: 'Tidak ada data',
            emptyTable: '<i class="fas fa-inbox mr-2"></i>Tidak ada data profit',
            zeroRecords: '<i class="fas fa-search mr-2"></i>Data tidak ditemukan',
            paginate: {
              first: '<i class="fas fa-angle-double-left"></i>',
              previous: '<i class="fas fa-angle-left"></i>',
              next: '<i class="fas fa-angle-right"></i>',
              last: '<i class="fas fa-angle-double-right"></i>',
            },
          },
        });

        // Filter change events (date changes simply redraw)
        $('#filterDateFrom, #filterDateTo').on('change', function () {
          profitTable.draw();
          updateStats();
          loadCustomers();
        });

        // Load customers for the dropdown based on date filters
        function loadCustomers() {
          var from = $('#filterDateFrom').val();
          var to = $('#filterDateTo').val();
          var current = $('#filterCustomer').val();

          $.getJSON('{{ route('admin.profit-margin.customers') }}', {
            filterDateFrom: from,
            filterDateTo: to,
          })
            .done(function (resp) {
              var $sel = $('#filterCustomer');
              $sel.empty();
              $sel.append('<option value="">-- Semua Customer --</option>');
              if (resp.customers && resp.customers.length) {
                resp.customers.forEach(function (c) {
                  var selected = current && current == c.id ? ' selected' : '';
                  $sel.append(
                    '<option value="' + c.id + '"' + selected + '>' + c.nama_pelanggan + '</option>'
                  );
                });
              } else {
                $sel.append('<option value="">-- Tidak ada customer --</option>');
              }
            })
            .fail(function () {
              // fallback: keep current selection
            });
        }

        // initial load of customers
        loadCustomers();

        // Customer change: simply redraw and let draw handler show action if needed
        $('#filterCustomer').on('change', function () {
          profitTable.draw();
          updateStats();
        });

        // Show an alert when table is empty for current filters and provide an action
        profitTable.on('draw', function () {
          var api = profitTable; // DataTables API instance already
          var totalRows = api.rows({ search: 'applied' }).count();

          if (totalRows === 0) {
            var customer = $('#filterCustomer').val();
            if (!customer) {
              $('#filterAlertText').text('Tidak ada transaksi untuk rentang tanggal yang dipilih.');
              $('#filterAlertAction').addClass('d-none');
              $('#filterAlert')
                .removeClass('d-none alert-success alert-warning')
                .addClass('alert-warning');
            } else {
              // check whether customer has transactions outside range
              $.getJSON('{{ route('admin.profit-margin.check') }}', {
                filterCustomer: customer,
                filterDateFrom: $('#filterDateFrom').val(),
                filterDateTo: $('#filterDateTo').val(),
              })
                .done(function (resp) {
                  if (resp.countFiltered === 0 && resp.countAll > 0) {
                    $('#filterAlertText').text(
                      'Tidak ada transaksi untuk customer ini pada rentang tanggal terpilih.'
                    );
                    $('#filterAlertAction')
                      .removeClass('d-none')
                      .off('click')
                      .on('click', function () {
                        $('#filterDateFrom').val('');
                        $('#filterDateTo').val('');
                        $('#filterAlert').addClass('d-none');
                        profitTable.draw();
                        updateStats();
                      });
                    $('#filterAlert')
                      .removeClass('d-none alert-success alert-warning')
                      .addClass('alert-info');
                  } else {
                    $('#filterAlertText').text(
                      'Tidak ada transaksi untuk rentang tanggal yang dipilih.'
                    );
                    $('#filterAlertAction').addClass('d-none');
                    $('#filterAlert')
                      .removeClass('d-none alert-success alert-info')
                      .addClass('alert-warning');
                  }
                })
                .fail(function () {
                  $('#filterAlertText').text(
                    'Tidak ada transaksi untuk rentang tanggal yang dipilih.'
                  );
                  $('#filterAlertAction').addClass('d-none');
                  $('#filterAlert')
                    .removeClass('d-none alert-success alert-info')
                    .addClass('alert-warning');
                });
            }
          } else {
            $('#filterAlert').addClass('d-none');
          }
        });

        // Add tooltip support and createdRow for product title
        profitTable.on('draw.dt', function () {
          $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover' });
        });

        // Add createdRow to set title attributes for product cell
        profitTable.on('xhr.dt', function (e, settings, json, xhr) {
          // nothing to do here for now
        });

        // Ensure initial tooltips
        $('[data-toggle="tooltip"]').tooltip({ trigger: 'hover' });

        // Reset filters
        $('#resetFilters').on('click', function () {
          $('#filterDateFrom').val('');
          $('#filterDateTo').val('');
          $('#filterCustomer').val('');
          profitTable.draw();
          updateStats();
        });

        // Export Excel button
        $('#exportExcel').on('click', function () {
          var url = '{{ route('admin.profit-margin.export') }}';
          var params = [];
          if ($('#filterDateFrom').val())
            params.push('filterDateFrom=' + encodeURIComponent($('#filterDateFrom').val()));
          if ($('#filterDateTo').val())
            params.push('filterDateTo=' + encodeURIComponent($('#filterDateTo').val()));
          if ($('#filterCustomer').val())
            params.push('filterCustomer=' + encodeURIComponent($('#filterCustomer').val()));
          if (params.length > 0) url += '?' + params.join('&');
          window.location = url;
        });

        // Initial stats update
        updateStats();
      }

      function updateStats() {
        $.ajax({
          url: '{{ route('admin.profit-margin.stats') }}',
          type: 'GET',
          data: {
            filterDateFrom: $('#filterDateFrom').val(),
            filterDateTo: $('#filterDateTo').val(),
            filterCustomer: $('#filterCustomer').val(),
          },
          success: function (response) {
            $('#totalPenjualan').text(response.total_penjualan_formatted || 'Rp 0');
            $('#totalModal').text(response.total_modal_formatted || 'Rp 0');
            $('#totalProfit').text(response.total_profit_formatted || 'Rp 0');
            $('#avgMargin').text(response.avg_margin_formatted || '0%');
          },
          error: function (xhr) {
            console.error('Failed to fetch stats:', xhr);
          },
        });
      }

      // Initialize when document is ready
      if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
          waitForDependencies(initTable);
        });
      } else {
        waitForDependencies(initTable);
      }
    </script>
  @endpush
</div>

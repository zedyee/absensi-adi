@extends('layouts.app')

@section('content')

    <style>
        #absenceTable {
            font-size: 0.800rem;
            /* setara ~14px */
        }

        #absenceTable th,
        #absenceTable td {
            padding: 6px 10px;
            /* biar tetap rapat dan rapi */
            vertical-align: middle;
        }

        .dataTables_filter {
            float: left !important;
            text-align: left !important;
            margin-bottom: 8px !important;
        }
    </style>

    <div class="card card-secondary card-outline mb-3" style="width: 980px;">
        <div class="card-body">
            <div class="d-flex flex-row align-items-center mb-2">
                {{-- title --}}
                <div class="my-0" style="width: 20%;">
                    <h3 class="m-0 mb-2">Data Absensi</h3>
                </div>

                <div class="d-flex justify-content-start me-2">
                    <div class="d-flex align-items-center gap-2 me-2">
                        <label for="searchDate" class="form-label fw-bold small mb-0">Tanggal:</label>
                        <input type="text" id="searchDate" name="daterange" class="form-control"
                            style="font-size: 12px; line-height: 1.5; height: calc(2.25rem + 2px);"
                            value="{{ \Carbon\Carbon::parse($startDate)->format('Y/m/d') . ' - ' . \Carbon\Carbon::parse($endDate)->format('Y/m/d') }}">
                    </div>

                    <div class="d-flex align-items-center gap-2"">

                        {{-- filter office --}}
                        <label for="searchOffice" class="form-label fw-bold small mb-0">Office:</label>
                        <select id="searchOffice" class="form-select">
                            @foreach ($offices as $office)
                                <option value="{{ $office->Alias }}" {{ $alias == $office->Alias ? 'selected' : '' }}>
                                    {{ $office->Alias }}
                                </option>
                            @endforeach
                        </select>

                        {{-- filter department --}}
                        <label for="searchDepartment" class="form-label fw-bold small me-2 mb-0">Department:</label>
                        <select id="searchDepartment" class="form-select form-select-sm">
                            <option value="All" {{ $dept == 'All' ? 'selected' : '' }}>All</option>
                            @foreach ($departments as $department)
                                <option value="{{ $department->DeptName }}"
                                    {{ $dept == $department->DeptName ? 'selected' : '' }}>
                                    {{ $department->DeptName }}
                                </option>
                            @endforeach
                        </select>

                    </div>
                </div>

                {{-- ekspor --}}
                <div class="btn-group ms-auto" role="group">
                    {{-- <button type="button" class="btn btn-excel px-4" style="background-color: #BDE3C3;">Excel</button> --}}
                    <button type="button" class="btn btn-print px-4" style="background-color: #bdcfe3ff;">Print</button>
                </div>
            </div>

            <table id="absenceTable" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>NIP</th>
                        <th>Nama</th>
                        <th>Office</th>
                        <th>Dept</th>
                        @foreach ($tanggalList as $tgl)
                            @php
                                // Ambil dua bagian terakhir
                                $parts = explode(' ', $tgl);
                                $day = $parts[count($parts) - 2] ?? '';
                                $date = $parts[count($parts) - 1] ?? '';
                            @endphp
                            <th style="font-size: 9px; text-align: center;">
                                {{ $day }}<br>{{ $date }}
                            </th>
                        @endforeach

                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $row)
                        <tr>
                            <td>{{ $row['nip'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>{{ $row['office'] }}</td>
                            <td>{{ $row['department'] }}</td>
                            @foreach ($tanggalList as $tgl)
                                <td>{{ $row[$tgl] ?? '-' }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

@endsection

<!-- js scripts -->
@section('scripts')

    <script>
        $(document).ready(function() {
            // Inisialisasi DataTable
            var table = $('#absenceTable').DataTable({
                paging: false,
                dom: '<"top"f>rt<"bottom"ip><"clear">'
            });

            // === Inisialisasi DateRangePicker ===
            var initial = $('#searchDate').val();
            var start = moment(initial.split(' - ')[0], 'YYYY/MM/DD');
            var end = moment(initial.split(' - ')[1], 'YYYY/MM/DD');

            $('#searchDate').daterangepicker({
                startDate: start,
                endDate: end,
                locale: {
                    format: 'YYYY/MM/DD',
                    separator: ' - ',
                    applyLabel: 'Apply',
                    cancelLabel: 'Cancel',
                    daysOfWeek: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    monthNames: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ]
                },
                opens: 'left',
                autoUpdateInput: true
            });

            // Saat user klik Apply pada DateRangePicker
            $('#searchDate').on('apply.daterangepicker', function(ev, picker) {
                redirectToReport();
            });

            // Saat user ubah Office
            $('#searchOffice').on('change', function() {
                redirectToReport();
            });

            // Saat user ubah Department
            $('#searchDepartment').on('change', function() {
                redirectToReport();
            });

            // === Fungsi utama untuk redirect ===
            function redirectToReport() {
                var alias = $('#searchOffice').val() || 'HO';
                var dept = $('#searchDepartment').val() || 'All';
                var daterange = $('#searchDate').val();

                var startDate = '';
                var endDate = '';

                if (daterange.includes(' - ')) {
                    var parts = daterange.split(' - ');
                    startDate = parts[0].trim().replaceAll('/', '-');
                    endDate = parts[1].trim().replaceAll('/', '-');
                }

                var url =
                    `{{ route('report') }}?alias=${encodeURIComponent(alias)}&DeptName=${encodeURIComponent(dept)}&startDate=${encodeURIComponent(startDate)}&endDate=${encodeURIComponent(endDate)}`;
                window.location.href = url;
            }

            // === Tombol Excel ===
            // $('.btn-excel').on('click', function() {
            //     var today = new Date().toISOString().split('T')[0];
            //     var tableClone = $('#absenceTable').clone();
            //     tableClone.find('tbody tr').each(function(i, row) {
            //         if ($(row).css('display') === 'none') {
            //             $(row).remove();
            //         }
            //     });
            //     var wb = XLSX.utils.table_to_book(tableClone[0], {
            //         sheet: "Data Report Absensi"
            //     });
            //     XLSX.writeFile(wb, `report_absensi_${today}.xlsx`);
            // });

            // === Tombol Print ===
            $('.btn-print').on('click', function() {
                var rows = $('#absenceTable tbody tr:visible').clone();

                // Ambil range tanggal dari input
                var dateRange = $('#searchDate').val(); // Contoh: 2025/10/01 - 2025/10/15
                var [startDate, endDate] = dateRange.split(' - ');
                var formattedRange = `${startDate} - ${endDate}`;

                // Ambil nama office
                var officeText = $('#searchOffice option:selected').text() || 'All';
                var deptText = $('#searchDepartment option:selected').text() || 'All';

                // Buat HTML print
                var html = `
    <html>
    <head>
        <title>REP_${formattedRange}_${officeText}_${deptText}</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { font-size: 0.875rem; padding: 0px; margin: 0px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #ccc; padding: 4px 6px; text-align: left; vertical-align: middle; }
            th { background: #898989; }
            .header-row {
                display: flex;
                justify-content: space-between;
                align-items: flex-end;
                margin-bottom: 4px;
                padding-bottom: 4px;
            }
            .header-row h2 {
                margin: 0;
                font-size: 1.2rem;
                font-weight: 600;
            }
        </style>
    </head>
    <body>
        <div class="header-row d-flex justify-content-between align-items-center">
            <!-- Kiri -->
            <div>
                <h2 class="mb-0">Data Report Absensi</h2>
                <span>${formattedRange}</span>
            </div>

            <!-- Tengah (Logo + Nama) -->
            <div class="d-flex flex-column align-items-center">
                <div class="d-flex align-items-center">
                    <img class="ms-2 me-2" src="public/img/logo-eratel.png" alt="Logo"
                        style="width:40px; height:40px; object-fit:contain;">
                    <h4 class="fw-bold text-secondary">Eratel Prima</h4>
                </div>
            </div>

            <!-- Kanan -->
            <div class="text-end">
                <span><strong>Office:</strong> ${officeText}</span><br>
                <span><strong>Dept:</strong> ${deptText}</span>
            </div>
        </div>

        <table class="table table-bordered table-striped">
            <thead style="font-size: 0.8rem;" class="table-dark">${$('#absenceTable thead').html()}</thead>
            <tbody>
                ${rows.map(function () {
                    return `<tr style="font-size: 0.8rem;">${$(this).html()}</tr>`;
                }).get().join('')}
            </tbody>
        </table>
    </body>
    </html>
    `;

                var printWindow = window.open('', '_blank');
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.focus();

                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            });
        });
    </script>

    <!-- Tambahkan library XLSX untuk ekspor Excel -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

@endsection

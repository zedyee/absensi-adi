@extends('layouts.app')

@section('content')

    <style>
        #officeTable {
            font-size: 0.875rem;
            /* setara ~14px */
        }

        #officeTable th,
        #officeTable td {
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

    <div class="card card-secondary card-outline mb-3">
        <div class="card-body">
            {{-- header --}}
            <div class="d-flex flex-row align-items-center justify-content-start mb-2">
                <div class="my-0" style="width: 20%;">
                    <h3 class="m-0 mb-2">Data Office</h3>
                </div>
                <div class="btn-group ms-auto" role="group">
                    <button type="button" class="btn btn-excel px-4" style="background-color: #BDE3C3;">Excel</button>
                    <button type="button" class="btn btn-print px-4" style="background-color: #bdcfe3ff;">Print</button>
                </div>
            </div>

            {{-- tabel --}}
            <table id="officeTable" class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 20%;">ID</th>
                        <th>Nama Office</th>
                        <th style="width: 15%;">Jumlah Karyawan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($offices as $off)
                        <tr>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#officeModal{{ $off->SN }}"
                                    style="text-decoration: none; color: black;">
                                    {{ $off->SN }}
                                </a>
                            </td>
                            <td>{{ $off->Alias }}</td>
                            <td>{{ $off->employees_count }}</td>
                        </tr>

                        {{-- modal edit office --}}
                        <div class="modal fade" id="officeModal{{ $off->SN }}" tabindex="-1"
                            aria-labelledby="officeModalTitle{{ $off->SN }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-secondary text-white">
                                        <h5 class="modal-title" id="officeModalTitle{{ $off->SN }}">
                                            Edit: {{ $off->Alias }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    {{-- Form Edit --}}
                                    <form method="POST" action="{{ url('office-update/' . $off->SN) }}">
                                        @csrf @method('PUT') {{-- Tambahkan ini agar sesuai RESTful update --}}

                                        {{-- form edit --}}
                                        <div class="modal-body">
                                            <div class="row">
                                                {{-- SN --}}
                                                <div class="form-group mb-2 col-8">
                                                    <label>SN</label>
                                                    <input type="text" class="form-control" value="{{ $off->SN }}"
                                                        disabled>
                                                </div>

                                                {{-- count karyawan --}}
                                                <div class="form-group mb-2 col-4">
                                                    <label>Jumlah Karyawan</label>
                                                    <input type="text" class="form-control"
                                                        value="{{ $off->employees_count }}" disabled>
                                                </div>
                                            </div>

                                            {{-- Alias --}}
                                            <div class="form-group mb-2">
                                                <label>Nama Office</label>
                                                <input type="text" name="alias" class="form-control"
                                                    value="{{ $off->Alias }}" required>
                                            </div>
                                        </div>

                                        {{-- save button --}}
                                        <div class="modal-footer d-flex justify-content-end">
                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Tidak ada data office.</td>
                        </tr>
                    @endforelse
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
            var table = $('#officeTable').DataTable({
                paging: false,
                dom: '<"top"f>rt<"bottom"ip><"clear">'
            });

            // === Tombol Excel ===
            $('.btn-excel').on('click', function() {
                // Pastikan SheetJS sudah dimuat
                if (typeof XLSX === 'undefined') {
                    $.getScript('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js',
                        function() {
                            exportExcel();
                        });
                } else {
                    exportExcel();
                }

                function exportExcel() {
                    // Ambil header tabel
                    var headers = [];
                    $('#officeTable thead th').each(function() {
                        headers.push($(this).text().trim());
                    });

                    // Ambil baris yang sedang terlihat
                    var rows = [];
                    $('#officeTable tbody tr:visible').each(function() {
                        var row = [];
                        $(this).find('td').each(function() {
                            row.push($(this).text().trim());
                        });
                        rows.push(row);
                    });

                    // Buat worksheet dari array
                    var worksheet_data = [headers, ...rows];
                    var worksheet = XLSX.utils.aoa_to_sheet(worksheet_data);

                    // Buat workbook dan tambahkan worksheet
                    var workbook = XLSX.utils.book_new();
                    XLSX.utils.book_append_sheet(workbook, worksheet, "Employee");

                    // Simpan file
                    var today = new Date().toISOString().split('T')[0];
                    XLSX.writeFile(workbook, 'employee' + today + '.xlsx');
                }
            });

            // === Tombol Print ===

            $('.btn-print').on('click', function() {
                // Ambil hanya baris yang sedang ditampilkan (tidak di-hide)
                var rows = $('#officeTable tbody tr:visible').clone();

                // Ambil tanggal hari ini (format: 22 Okt 2025)
                var today = new Date();
                var options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };
                var formattedDate = today.toLocaleDateString('id-ID', options);

                // Siapkan HTML print
                var html = `
                <html>
                <head>
                    <title>OFF_${formattedDate}</title>
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
                            <h2>Data Office</h2>
                            <span>${formattedDate}</span>
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
                            <span> </span>
                        </div>
                    </div>

                    <table class="table table-bordered table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th style="width:15%;">ID</th>
                                <th>Nama Office</th>
                                <th style="width:15%;">Jumlah Karyawan</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${rows.map(function() {
                                return `<tr>${$(this).html()}</tr>`;
                            }).get().join('')}
                        </tbody>
                    </table>
                </body>
                </html>
            `;

                // Buka jendela baru untuk print
                var printWindow = window.open('', '_blank');
                printWindow.document.write(html);
                printWindow.document.close();
                printWindow.focus();

                // Tunggu sebentar lalu print
                setTimeout(function() {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            });

            // notif success edit
            @if (session('success'))
                Swal.fire({
                    title: 'Success!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    </script>

@endsection

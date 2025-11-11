@extends('layouts.app')

@section('content')

    <style>
        #employeeTable {
            font-size: 0.875rem;
            /* setara ~14px */
        }

        #employeeTable th,
        #employeeTable td {
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
            <div class="d-flex flex-row align-items-center justify-content-start mb-2">
                {{-- title --}}
                <div class="my-0" style="width: 25%;">
                    <h3 class="m-0">Data Karyawan</h3>
                </div>

                <div class="d-flex align-items-center gap-2"">
                    {{-- filter office --}}
                    <label for="searchOffice" class="form-label fw-bold small me-2 mb-0">Office:</label>
                    <select id="searchOffice" class="form-select form-select-sm">
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

                {{-- button --}}
                <div class="btn-group ms-auto" role="group">
                    {{-- <button type="button" class="btn btn-add px-4" style="background-color: #BDE3C3;">Tambah</button> --}}
                    <button type="button" class="btn btn-excel px-4" style="background-color: #BDE3C3;">Excel</button>
                    <button type="button" class="btn btn-print px-4" style="background-color: #bdcfe3ff;">Print</button>
                </div>

            </div>

            {{-- table --}}
            <table id="employeeTable" class="table table-bordered table-striped table-hover">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 7%;">No</th>
                        <th style="width: 15%;">NIP</th>
                        <th>Nama</th>
                        <th style="width: 15%;">Office</th>
                        <th style="width: 15%;">Department</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($employees as $emp)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#employeeModal{{ $emp->userid }}"
                                    style="text-decoration: none; color: black;">
                                    {{ $emp->badgenumber }}
                                </a>
                            </td>
                            <td>{{ $emp->name }}</td>
                            <td>{{ $emp->office->Alias ?? '-' }}</td>
                            <td>{{ $emp->department->DeptName ?? '-' }}</td>
                        </tr>

                        <!-- Modal Edit untuk tiap karyawan -->
                        <div class="modal fade" id="employeeModal{{ $emp->userid }}" tabindex="-1"
                            aria-labelledby="employeeModalTitle{{ $emp->userid }}" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header bg-secondary text-white">
                                        <h5 class="modal-title" id="employeeModalTitle{{ $emp->userid }}">
                                            Edit: {{ $emp->name }}
                                        </h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>

                                    {{-- Form Edit --}}
                                    <form method="POST" action="{{ url('employee-update/' . $emp->userid) }}">
                                        @csrf
                                        @method('PUT') {{-- Tambahkan ini agar sesuai RESTful update --}}

                                        <div class="modal-body">
                                            {{-- NIP --}}
                                            <div class="form-group mb-2">
                                                <label>NIP</label>
                                                <input type="text" class="form-control" value="{{ $emp->badgenumber }}"
                                                    disabled>
                                            </div>

                                            {{-- Nama --}}
                                            <div class="form-group mb-2">
                                                <label>Nama</label>
                                                <input type="text" name="name" class="form-control"
                                                    value="{{ $emp->name }}" required>
                                            </div>

                                            <div class="row form-group mb-2">
                                                {{-- Office --}}
                                                <div class="col-6">
                                                    <label>Office</label>
                                                    <select name="office" class="form-select">
                                                        @foreach ($offices as $office)
                                                            <option value="{{ $office->Alias }}"
                                                                {{ $office->Alias == ($emp->office->Alias ?? '') ? 'selected' : '' }}>
                                                                {{ $office->Alias }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                {{-- Department --}}
                                                <div class="col-6">
                                                    <label>Department</label>
                                                    <select name="department" class="form-select">
                                                        @foreach ($departments as $department)
                                                            <option value="{{ $department->DeptName }}"
                                                                {{ $department->DeptName == ($emp->department->DeptName ?? '') ? 'selected' : '' }}>
                                                                {{ $department->DeptName }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="modal-footer d-flex justify-content-between">
                                            {{-- Tombol Hapus --}}
                                            <a href="{{ url('employee-delete/' . $emp->userid) }}"
                                                class="btn btn-danger btn-delete" data-name="{{ $emp->name }}"
                                                data-nip="{{ $emp->badgenumber }}">
                                                Hapus
                                            </a>

                                            <button type="submit" class="btn btn-success">Simpan</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
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
            var table = $('#employeeTable').DataTable({
                paging: false,
                dom: '<"top"f>rt<"bottom"ip><"clear">'
            });

            // === Dropdown Office & Department Filter ===
            $('#searchOffice, #searchDepartment').on('change', function() {
                var selectedOffice = $('#searchOffice').val();
                var selectedDept = $('#searchDepartment').val();
                var baseUrl = "{{ route('dashboard') }}";

                // Bangun query string berdasarkan kombinasi pilihan
                var params = [];

                if (selectedOffice) {
                    params.push('alias=' + encodeURIComponent(selectedOffice));
                }

                if (selectedDept) {
                    params.push('DeptName=' + encodeURIComponent(selectedDept));
                }

                var url = baseUrl + (params.length ? '?' + params.join('&') : '');
                window.location.href = url;
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
                    $('#employeeTable thead th').each(function() {
                        headers.push($(this).text().trim());
                    });

                    // Ambil baris yang sedang terlihat
                    var rows = [];
                    $('#employeeTable tbody tr:visible').each(function() {
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
                var rows = $('#employeeTable tbody tr:visible').clone();

                // Ambil nilai filter (ganti id sesuai form filter milikmu)
                var selectedOffice = $('#searchOffice').val();
                var selectedDept = $('#searchDepartment').val();

                // Tampilkan label jika tidak dipilih
                var officeText = selectedOffice && selectedOffice !== '' ? selectedOffice : 'All';
                var deptText = selectedDept && selectedDept !== '' ? selectedDept : 'All';

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
            <title>EMP_${selectedOffice}_${selectedDept}_${formattedDate}</title>
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
                    <h2>Data Karyawan</h2>
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
                    <span><strong>Office:</strong> ${officeText}</span> </br>
                    <span><strong>Dept:</strong> ${deptText}</span>
                </div>
            </div>

            <table class="table table-bordered table-striped">
                <thead class="table-dark">
                    <tr>
                        <th style="width:7%;">No</th>
                        <th style="width:10%;">ID</th>
                        <th>Nama</th>
                        <th style="width:15%;">Office</th>
                        <th style="width:15%;">Department</th>
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
        });

        // konfirmasi delete
        document.addEventListener('DOMContentLoaded', function() {
            const deleteLinks = document.querySelectorAll('.btn-delete');

            deleteLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();

                    const url = this.getAttribute('href');
                    const name = this.dataset.name;
                    const nip = this.dataset.nip;

                    Swal.fire({
                        title: 'Yakin ingin menghapus?',
                        html: `<b>${name}</b> (NIP: ${nip}) akan dihapus permanen.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Ya',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Redirect ke URL penghapusan
                            window.location.href = url;
                        }
                    });
                });
            });
        });

        @if (session('success'))
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonColor: '#3085d6',
                confirmButtonText: 'OK'
            });
        @endif
    </script>

@endsection

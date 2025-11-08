@extends('layouts.app')

@section('title', 'Eratel | Absensi')

@section('content')

<style>
    #departmentTable {
        font-size: 0.875rem;
        /* setara ~14px */
    }

    #departmentTable th,
    #departmentTable td {
        padding: 6px 10px;
        /* biar tetap rapat dan rapi */
        vertical-align: middle;
    }
</style>


<div class="card card-secondary card-outline mb-3">
    <div class="card-body">
        <div class="d-flex flex-row align-items-center gap-2">
            <div class="my-0" style="width: 25%;">
                <h3 class="m-0 mb-2">Data Departemen</h3>
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-excel" style="background-color: #BDE3C3;">Excel</button>
                    <button type="button" class="btn btn-pdf" style="background-color: #e3bdbdff;">PDF</button>
                    <button type="button" class="btn btn-print" style="background-color: #bdcfe3ff;">Print</button>
                </div>
            </div>

            <div class="my-0" style="width: 25%;">
                <label class="form-label fw-bold mb-1 small">Nama / ID</label>
                <input type="text" id="searchNama" class="form-control" placeholder="Cari nama atau ID...">
            </div>
        </div>
    </div>
</div>

<!-- Tabel Data -->
<div class="card">
    <div class="card-body">
        <table id="departmentTable" class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th style="width: 15%;">ID</th>
                    <th>Nama Departemen</th>
                    <th style="width: 15%;">Jumlah Karyawan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($departments as $dept)
                <tr>
                    <td>{{ $dept->DeptID }}</td>
                    <td>{{ $dept->DeptName }}</td>
                    <td>{{ $dept->employees_count }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center text-muted">Tidak ada data departemen.</td>
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
        var table = $('#departmentTable').DataTable({
            order: [
                [0, 'asc']
            ],
            pageLength: 50,
            lengthChange: false,
            searching: false
        });

        // Fungsi filter kustom
        function applyFilters() {
            var searchNama = $('#searchNama').val().toLowerCase();

            table.rows().every(function() {
                var data = this.data();

                var deptId = data[0].toString().toLowerCase(); // Kolom ID
                var deptName = data[1].toLowerCase(); // Kolom Nama

                // Cocokkan jika search term ada di ID atau Nama
                var match = !searchNama ||
                    deptId.includes(searchNama) ||
                    deptName.includes(searchNama);

                if (match) {
                    $(this.node()).show();
                } else {
                    $(this.node()).hide();
                }
            });
        }

        // Jalankan filter setiap kali input berubah
        $('#searchNama').on('input change', function() {
            applyFilters();
        });

        // === Tombol Excel ===
        $('.btn-excel').on('click', function() {
            // Pastikan SheetJS sudah dimuat
            if (typeof XLSX === 'undefined') {
                $.getScript('https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js', function() {
                    exportExcel();
                });
            } else {
                exportExcel();
            }

            function exportExcel() {
                // Ambil header tabel
                var headers = [];
                $('#departmentTable thead th').each(function() {
                    headers.push($(this).text().trim());
                });

                // Ambil baris yang sedang terlihat
                var rows = [];
                $('#departmentTable tbody tr:visible').each(function() {
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
                XLSX.utils.book_append_sheet(workbook, worksheet, "Departemen");

                // Simpan file
                var today = new Date().toISOString().split('T')[0];
                XLSX.writeFile(workbook, 'departemen_' + today + '.xlsx');
            }
        });

        // === Tombol PDF ===
        $('.btn-pdf').on('click', function() {
            // Pastikan jsPDF dan autoTable tersedia
            if (typeof window.jspdf === 'undefined') {
                $.getScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js', function() {
                    $.getScript('https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js', function() {
                        generatePDF();
                    });
                });
            } else {
                generatePDF();
            }

            function generatePDF() {
                const {
                    jsPDF
                } = window.jspdf;
                const doc = new jsPDF({
                    orientation: 'portrait',
                    unit: 'pt',
                    format: 'a4'
                });

                // Format tanggal hari ini (misal: 22 Okt 2025)
                var today = new Date();
                var options = {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric'
                };
                var formattedDate = today.toLocaleDateString('id-ID', options);

                // === Header dokumen ===
                doc.setFontSize(14);
                doc.text('Data Departemen', 40, 40);

                doc.setFontSize(10);
                doc.text(`Tanggal: ${formattedDate}`, 40, 58);

                // === Ambil baris yang terlihat ===
                var visibleRows = $('#departmentTable tbody tr:visible');
                var tableData = [];
                visibleRows.each(function() {
                    var cells = $(this).find('td').map(function() {
                        return $(this).text().trim();
                    }).get();
                    tableData.push(cells);
                });

                // === Ambil header ===
                var headers = [];
                $('#departmentTable thead th').each(function() {
                    headers.push($(this).text().trim());
                });

                // === Buat tabel di PDF ===
                doc.autoTable({
                    head: [headers],
                    body: tableData,
                    startY: 75, // digeser sedikit ke bawah supaya tidak menimpa tanggal
                    styles: {
                        fontSize: 9,
                        cellPadding: 5,
                        halign: 'left',
                        valign: 'middle'
                    },
                    headStyles: {
                        fillColor: [100, 100, 100],
                        textColor: 255
                    },
                    theme: 'grid'
                });

                // === Simpan file ===
                var dateFile = today.toISOString().split('T')[0];
                doc.save('departemen_' + dateFile + '.pdf');
            }
        });

        // === Tombol Print ===
        $('.btn-print').on('click', function() {
            // Ambil hanya baris yang sedang ditampilkan (tidak di-hide)
            var rows = $('#departmentTable tbody tr:visible').clone();

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
            <title>Print Data Departemen</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
            <style>
                body { font-size: 0.875rem; padding: 20px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #ccc; padding: 6px 10px; text-align: left; vertical-align: middle; }
                th { background: #f0f0f0; }
                .header-row {
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                    margin-bottom: 10px;
                    border-bottom: 2px solid #ccc;
                    padding-bottom: 4px;
                }
                .header-row h2 {
                    margin: 0;
                    font-size: 1.2rem;
                    font-weight: 600;
                }
                .header-meta {
                    text-align: right;
                    font-size: 0.9rem;
                }
                .header-meta span {
                    display: block;
                    line-height: 1.4;
                }
            </style>
        </head>
        <body>
            <div class="header-row">
                <div>
                    <h2>Data Department</h2>
                    <span>${formattedDate}</span>
                </div>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th style="width:15%;">ID</th>
                        <th>Nama Departemen</th>
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


    });
</script>

@endsection
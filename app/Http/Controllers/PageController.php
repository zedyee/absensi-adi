<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Office;
use App\Models\Department;
use App\Models\Absence;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

class PageController extends Controller
{
    public function employeeShow(Request $request)
    {
        // ambil data office dan dept untuk dropdown
        $offices = Office::select('SN', 'Alias')
            ->orderBy('Alias', 'asc')
            ->get();
        $departments = Department::select('DeptID', 'DeptName')
            ->orderBy('DeptName', 'asc')
            ->get();

        // Ambil parameter alias dan depts dari URL, jika kosong -> default 'HO'
        $alias = $request->input('alias', 'ADI-JakBar');
        $dept = $request->input('DeptName', default: 'All');

        // Query dasar: ambil employee berdasarkan office
        $employees = Employee::with(['office', 'department'])
            ->whereHas('office', function ($q) use ($alias) {
                $q->where('Alias', $alias);
            });

        // Jika dept bukan 'All', tambahkan filter berdasarkan department
        if (strtolower($dept) !== 'all') {
            $employees->whereHas('department', function ($q) use ($dept) {
                $q->where('DeptName', $dept);
            });
        }

        // Urutkan dan ambil hasil
        $employees = $employees->orderBy('badgenumber')->get();

        return view('pages.employee', compact('employees', 'offices', 'departments', 'alias', 'dept'));
    }

    public function employeeCreate(Request $request) {}

    public function employeeUpdate(Request $request)
    {
        $employee = Employee::findOrFail($request->id);

        // Cari SN dari tabel machines berdasarkan alias
        $office = Office::where('alias', $request->office)->first();
        // Cari DeptID dari tabel departments berdasarkan DeptName
        $department = Department::where('DeptName', $request->department)->first();

        // Update kolom yang sesuai
        $employee->name = $request->name;
        if ($office) {
            $employee->SN = $office->SN;
        }
        if ($department) {
            $employee->defaultdeptid = $department->DeptID;
        }

        $employee->save();

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    public function employeeDelete($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->back()->with('success', 'Data karyawan berhasil dihapus!');
    }

    public function officeShow()
    {
        // Ambil data office dan count karyawan
        $offices = Office::withCount('employees')
            ->orderBy('Alias', 'asc')
            ->get();

        return view('pages.office', compact('offices'));
    }

    public function officeUpdate(Request $request, $id)
    {
        $office = Office::findOrFail($id);
        // dd($request->all());

        // Update kolom yang sesuai
        $office->Alias = $request->alias;
        $office->save();

        return redirect()->back()->with('success', 'Data berhasil diperbarui!');
    }

    public function showReport(Request $request)
    {
        // Ambil data office dan department untuk dropdown
        $offices = Office::select('SN', 'Alias')
            ->orderBy('Alias', 'asc')
            ->get();
        $departments = Department::select('DeptID', 'DeptName')
            ->orderBy('DeptName', 'asc')
            ->get();

        // --- Ambil parameter alias dari request (default: 'HO')
        $alias = $request->input('alias', 'HO');
        $dept = $request->input('DeptName', default: 'All');

        // --- Ambil range tanggal dari request (default: 26 bulan lalu s.d. 25 bulan ini)
        $startDate = $request->input('startDate')
            ? Carbon::parse($request->input('startDate'))->startOfDay()
            : now()->subMonth()->day(26)->startOfDay();

        $endDate = $request->input('endDate')
            ? Carbon::parse($request->input('endDate'))->endOfDay()
            : now()->day(25)->endOfDay();

        // --- Ambil data absensi (filter berdasarkan office & tanggal)
        $absences = Absence::with(['employee.department', 'employee.office'])
            ->whereHas('employee.office', function ($q) use ($alias) {
                $q->where('Alias', $alias);
            })
            ->whereBetween('checktime', [$startDate, $endDate])
            ->get();

        // --- Buat daftar tanggal dalam range (termasuk weekend)
        $tanggalList = collect();
        $tanggal = Carbon::parse($startDate);
        while ($tanggal->lte(Carbon::parse($endDate))) {
            $tanggalList->push($tanggal->format('M y D d'));
            $tanggal->addDay();
        }

        // --- Kelompokkan absensi per karyawan
        $groupedByEmployee = $absences->groupBy('userid');
        $data = [];

        foreach ($groupedByEmployee as $userid => $records) {
            $employee = $records->first()->employee;

            $row = [
                'nip' => $employee->badgenumber ?? '-',
                'name' => $employee->name ?? '-',
                'office' => $employee->office->Alias ?? '-',
                'department' => $employee->department->DeptName ?? '-',
            ];

            // --- Isi jam per tanggal (kosong kalau tidak ada absen)
            foreach ($tanggalList as $tanggal) {
                $jam = $records
                    ->filter(fn($r) => Carbon::parse($r->checktime)->format('M y D d') === $tanggal)
                    ->pluck('checktime')
                    ->map(fn($t) => Carbon::parse($t)->format('H:i'))
                    ->sort() // urutkan dari jam paling kecil ke besar
                    ->values();

                // ambil hanya dua: pertama dan terakhir
                if ($jam->count() > 1) {
                    $jam = $jam->only([0, $jam->count() - 1]);
                }

                $row[$tanggal] = $jam->implode(' ');
            }

            $data[] = $row;
        }

        return view('pages.report', compact('data', 'offices', 'departments', 'tanggalList', 'alias', 'dept', 'startDate', 'endDate'));
    }
}

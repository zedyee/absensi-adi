<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    protected $connection = 'adms'; // 🔗 koneksi ke database adms
    protected $table = 'checkinout'; // nama tabel
    protected $primaryKey = 'id'; // kolom primary key
    public $timestamps = false; // tabel tidak punya created_at / updated_at

    protected $fillable = [
        'userid',
        'checktime',
        'checktype',
        'verifycode',
        'SN',
        'sensorid',
        'WorkCode',
        'Reserved',
    ];

    /**
     * Relasi ke karyawan (Employee)
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'userid', 'userid');
    }

    /**
     * Relasi ke office (mesin absensi)
     */
    public function office()
    {
        return $this->belongsTo(Office::class, 'SN', 'SN');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    protected $connection = 'adms'; // 🔗 pakai koneksi adms
    protected $table = 'userinfo';
    protected $primaryKey = 'userid'; // ganti sesuai nama kolom primary key kamu
    public $timestamps = false; // nonaktifkan timestamps kalau tabel tidak punya created_at, updated_at

    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'badgenumber',
        'name',
        'defaultdeptid',
        'SN',
    ];

    // Relasi ke offices
    public function office()
    {
        return $this->belongsTo(Office::class, 'SN', 'SN');
    }

    // Relasi ke departments
    public function department()
    {
        return $this->belongsTo(Department::class, 'defaultdeptid', 'DeptID');
    }

    public function absences(): HasMany
    {
        return $this->hasMany(Absence::class, 'userid', 'userid');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $connection = 'adms';
    protected $table = 'iclock';
    protected $primaryKey = 'SN';
    protected $keyType = 'string'; // ✅ tambahkan ini
    public $incrementing = false;  // ✅ tambahkan ini
    public $timestamps = false;

    protected $fillable = [
        'SN',
        'Alias',
        'IP',
    ];

    // Relasi ke employees
    public function employees()
    {
        return $this->hasMany(Employee::class, 'SN', 'SN');
    }
}

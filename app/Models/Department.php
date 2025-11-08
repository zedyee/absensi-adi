<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $connection = 'adms';
    protected $table = 'departments';
    protected $primaryKey = 'DeptID';
    public $timestamps = false;

    protected $fillable = [
        'DeptID',
        'DeptName',
    ];

    // Relasi ke employees
    public function employees()
    {
        return $this->hasMany(Employee::class, 'defaultdeptid', 'DeptID');
    }
}

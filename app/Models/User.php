<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * Gunakan koneksi database 'adms'
     */
    protected $connection = 'adms';

    /**
     * Nama tabel yang digunakan
     */
    protected $table = 'auth_user'; // ubah jika nama tabel berbeda

    /**
     * Kolom yang bisa diisi mass-assignment
     */
    protected $fillable = [
        'username',
        'password',
    ];

    /**
     * Kolom yang disembunyikan saat model di-serialize
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Cast atribut
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    /**
     * Ganti kolom yang digunakan untuk autentikasi
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
}

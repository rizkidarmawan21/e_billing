<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Package;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_pelanggan',
        'nama',
        'no_hp',
        'alamat',
        'package_id',
    ];

    public function package()
    {
        return $this->belongsTo(Package::class);
    }

     public function payments()
    {
        return $this->hasMany(Payment::class);
    }

}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegimenFiscal extends Model
{
    use HasFactory;
    protected $table = 'regimen_fiscales';

    protected $fillable = ['code', 'description'];
}

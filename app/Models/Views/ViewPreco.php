<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class ViewPreco extends Model
{
    protected $table = 'view_precos_processados';

    public $timestamps = false;

    public $incrementing = false;

    public static function boot() {
        parent::boot();
        static::creating(function () { return false; });
    }
}

<?php

namespace App\Models\Views;

use Illuminate\Database\Eloquent\Model;

class ViewProduto extends Model
{
    protected $table = 'view_produtos_processados';

    public $timestamps = false;

    public $incrementing = false;

    public static function boot() {
        parent::boot();
        static::creating(function () { return false; });
    }
}

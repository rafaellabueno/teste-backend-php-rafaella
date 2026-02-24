<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrecoInsercao extends Model
{
    protected $table = 'preco_insercao';

    protected $fillable = ['codigo_produto', 'valor', 'valor_promocional', 'status', 'created_at', 'updated_at'];

    public function produto()
    {
        return $this->belongsTo(ProdutoInsercao::class, 'codigo_produto', 'codigo');
    }
}

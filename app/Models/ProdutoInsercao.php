<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProdutoInsercao extends Model
{
    protected $table = 'produto_insercao';

    protected $fillable = ['codigo', 'nome', 'categoria', 'descricao', 'ativo', 'created_at', 'updated_at'];

    public function precos()
    {
        return $this->hasMany(PrecoInsercao::class, 'codigo_produto', 'codigo');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Views\ViewProduto;
use App\Models\Views\ViewPreco;
use App\Models\ProdutoInsercao;
use App\Models\PrecoInsercao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SincronizacaoController extends Controller
{
    public function sincronizarProdutos()
    {
        DB::beginTransaction();
        try {
            $produtosView = ViewProduto::all();
            
            $codigosProcessados = [];
            $inseridos = 0;
            $atualizados = 0;

            foreach ($produtosView as $produto) {
                $codigosProcessados[] = $produto->codigo;
                
                $atualizado = ProdutoInsercao::where('codigo', $produto->codigo)
                    ->update($produto->toArray());

                if ($atualizado) {
                    $atualizados++;
                } else {
                    ProdutoInsercao::create($produto->toArray());
                    $inseridos++;
                }
            }

            $removidos = ProdutoInsercao::whereNotIn('codigo', $codigosProcessados)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sincronização de produtos concluída com sucesso.',
                'operacoes' => [
                    'inseridos' => $inseridos,
                    'atualizados' => $atualizados,
                    'removidos' => $removidos,
                    'total_processados' => count($codigosProcessados)
                ]
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Falha na sincronização de produtos: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno ao sincronizar produtos.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function sincronizarPrecos()
    {
        DB::beginTransaction();
        try {
            $precosView = ViewPreco::all();
            
            $codigosProcessados = [];
            $inseridos = 0;
            $atualizados = 0;

            foreach ($precosView as $preco) {
                $produtoExiste = ProdutoInsercao::where('codigo', $preco->codigo_produto)->exists();
                
                if (!$produtoExiste) {
                    continue;
                }

                $codigosProcessados[] = $preco->codigo_produto;
                
                $atualizado = PrecoInsercao::where('codigo_produto', $preco->codigo_produto)
                    ->update($preco->toArray());

                if ($atualizado) {
                    $atualizados++;
                } else {
                    PrecoInsercao::create($preco->toArray());
                    $inseridos++;
                }
            }

            $removidos = PrecoInsercao::whereNotIn('codigo_produto', $codigosProcessados)->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Sincronização de preços concluída com sucesso.',
                'operacoes' => [
                    'inseridos' => $inseridos,
                    'atualizados' => $atualizados,
                    'removidos' => $removidos,
                    'total_processados' => count($codigosProcessados)
                ]
            ], 200);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Falha na sincronização de preços: " . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno ao sincronizar preços.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function listarProdutos(Request $request)
    {
        try {
            $perPage = $request->query('per_page', 10);
            
            if (!is_numeric($perPage) || $perPage < 1 || $perPage > 100) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'O parâmetro per_page deve ser um número entre 1 e 100.'
                ], 400); 
            }

            $produtos = ProdutoInsercao::with(['precos'])
                ->paginate((int)$perPage);

            return response()->json($produtos, 200);

        } catch (Exception $e) {
            Log::error("Erro ao listar produtos: " . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Erro interno ao recuperar lista de produtos.',
                'details' => config('app.debug') ? $e->getMessage() : 'Ocorreu um erro inesperado.'
            ], 500);
        }
    }
}
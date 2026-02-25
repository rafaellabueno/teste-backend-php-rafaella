<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;      
use App\Models\PrecoInsercao; 
use App\Models\ProdutoInsercao;
use PHPUnit\Framework\Attributes\Test;

class SincronizacaoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\BaseDataSeeder::class);
    }

    #[Test]
    public function deve_sincronizar_e_normalizar_produtos_removendo_espacos_e_acentos()
    {
        $this->postJson('/api/sincronizar/produtos');

        $this->assertDatabaseHas('produto_insercao', [
            'codigo' => 'PRD002',
            'nome' => 'Mouse óptico sem fio',
            'categoria' => 'PERIFERICOS' 
        ]);
    }

    #[Test]
    public function deve_remover_do_destino_produtos_excluidos_da_origem()
    {
        $this->postJson('/api/sincronizar/produtos');
        
        DB::table('produtos_base')->where('prod_cod', 'like', '%PRD001%')->delete();

        $response = $this->postJson('/api/sincronizar/produtos');

        $this->assertDatabaseMissing('produto_insercao', ['codigo' => 'PRD001']);
        
        $response->assertJsonPath('operacoes.removidos', 1);
    }

    #[Test]
    public function deve_sincronizar_precos_convertendo_para_float_corretamente()
    {
        $this->postJson('/api/sincronizar/produtos');
        $this->postJson('/api/sincronizar/precos');

        $this->assertDatabaseHas('preco_insercao', [
            'codigo_produto' => 'PRD002',
            'valor' => 120.5
        ]);

        $this->assertDatabaseHas('preco_insercao', [
            'codigo_produto' => 'PRD003',
            'valor' => 1099.00
        ]);
    }

    #[Test]
    public function deve_retornar_listagem_com_relacionamento_e_paginacao()
    {
        $this->postJson('/api/sincronizar/produtos');
        $this->postJson('/api/sincronizar/precos');

        $response = $this->getJson('/api/produtos-precos?per_page=5');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'codigo', 
                        'nome', 
                        'precos' => [ 
                            '*' => ['valor', 'status']
                        ]
                    ]
                ],
                'total'
            ]);
    }

    #[Test]
    public function nao_deve_sincronizar_precos_se_a_tabela_de_produtos_estiver_vazia()
    {
        $response = $this->postJson('/api/sincronizar/precos');

        $response->assertStatus(200);
        
        $this->assertEquals(0, PrecoInsercao::count());
    }

    #[Test]
    public function deve_atualizar_produto_existente()
    {
        $this->postJson('/api/sincronizar/produtos');
        $produtoOriginal = ProdutoInsercao::where('codigo', 'PRD001')->first();
        
        sleep(1); 

        $response = $this->postJson('/api/sincronizar/produtos');
        $produtoAtualizado = ProdutoInsercao::where('codigo', 'PRD001')->first();

        $this->assertEquals($produtoOriginal->id, $produtoAtualizado->id, 'O ID do produto não deve mudar.');
        $this->assertEquals($produtoOriginal->created_at, $produtoAtualizado->created_at, 'O timestamp de criação não deve mudar.');
        $this->assertNotEquals($produtoOriginal->updated_at, $produtoAtualizado->updated_at, 'O timestamp de atualização deve mudar.');
        $this->assertGreaterThanOrEqual(1, $response->json('operacoes.atualizados'));
    }

    #[Test]
    public function deve_inserir_e_atualizar_produtos_simultaneamente()
    {
        $this->postJson('/api/sincronizar/produtos');

        DB::table('produtos_base')->insert([
            'prod_id' => 999, 'prod_cod' => 'PRD999', 'prod_nome' => 'Produto Novo', 
            'prod_cat' => 'TESTE', 'prod_atv' => 1
        ]);

        $response = $this->postJson('/api/sincronizar/produtos');

        $this->assertDatabaseHas('produto_insercao', ['codigo' => 'PRD999']);
        $response->assertJsonPath('operacoes.inseridos', 1);
        $this->assertGreaterThanOrEqual(1, $response->json('operacoes.atualizados'));
    }

    #[Test]
    public function deve_manter_consistencia_apos_multiplas_sincronizacoes()
    {
        for ($i = 0; $i < 3; $i++) {
            $this->postJson('/api/sincronizar/produtos')->assertStatus(200);
            $this->postJson('/api/sincronizar/precos')->assertStatus(200);
        }

        $this->assertEquals(1, ProdutoInsercao::where('codigo', 'PRD001')->count(), 'Não deve haver produtos duplicados.');
        $this->assertEquals(1, PrecoInsercao::where('codigo_produto', 'PRD001')->count(), 'Não deve haver preços duplicados.');
        
        $precosOrfaos = PrecoInsercao::whereNotIn('codigo_produto', ProdutoInsercao::pluck('codigo'))->count();
        $this->assertEquals(0, $precosOrfaos, 'Não deve haver preços órfãos.');
    }

    #[Test]
    public function deve_rejeitar_per_page_invalido()
    {
        $this->postJson('/api/sincronizar/produtos');
        $this->postJson('/api/sincronizar/precos');

        $response = $this->getJson('/api/produtos-precos?per_page=999');
        
        $response->assertStatus(400);
    }
}

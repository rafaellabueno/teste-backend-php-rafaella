<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;      
use App\Models\PrecoInsercao;      
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
            'categoria' => 'PERIFERICOS' 
        ]);
    }

    #[Test]
    public function deve_remover_do_destino_produtos_excluidos_da_origem()
    {
        $this->postJson('/api/sincronizar/produtos');
        
        DB::table('produtos_base')->where('prod_cod', 'like', '%PRD001%')->delete();

        $this->postJson('/api/sincronizar/produtos');

        $this->assertDatabaseMissing('produto_insercao', ['codigo' => 'PRD001']);
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
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            CREATE VIEW view_produtos_processados AS
            SELECT 
                UPPER(TRIM(prod_cod)) as codigo,
                TRIM(prod_nome) as nome,
                UPPER(TRIM(prod_cat)) as categoria,
                prod_desc as descricao,
                prod_atv as ativo
            FROM produtos_base
            WHERE prod_atv = 1
        ");

        DB::statement("
            CREATE VIEW view_precos_processados AS
            SELECT 
                UPPER(TRIM(prc_cod_prod)) as codigo_produto,
                CAST(
                    CASE 
                        WHEN prc_valor LIKE '%,%' THEN REPLACE(REPLACE(TRIM(prc_valor), '.', ''), ',', '.')
                        ELSE TRIM(prc_valor)
                    END 
                AS DECIMAL(10,2)) as valor,
                CASE 
                    WHEN prc_promo LIKE '%sem preço%' OR prc_promo = '' OR prc_promo IS NULL THEN NULL 
                    ELSE CAST(
                        CASE 
                            WHEN prc_promo LIKE '%,%' THEN REPLACE(REPLACE(TRIM(prc_promo), '.', ''), ',', '.')
                            ELSE TRIM(prc_promo)
                        END 
                    AS DECIMAL(10,2))
                END as valor_promocional,
                TRIM(prc_status) as status
            FROM precos_base
            WHERE prc_status = 'ativo'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_precos_limpos");
        DB::statement("DROP VIEW IF EXISTS view_produtos_limpos");
    }
};

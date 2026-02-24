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
        DB::statement("DROP VIEW IF EXISTS view_produtos_processados");
        DB::statement("DROP VIEW IF EXISTS view_precos_processados");

        DB::statement("
            CREATE VIEW view_produtos_processados AS
            SELECT 
                UPPER(TRIM(prod_cod)) AS codigo,
                REPLACE(REPLACE(REPLACE(TRIM(prod_nome), '   ', ' '), '  ', ' '), '  ', ' ') AS nome,
                UPPER(
                    REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(
                        TRIM(prod_cat), 
                    'É', 'E'), 'é', 'E'), 'Í', 'I'), 'í', 'I'), 
                    'Ó', 'O'), 'ó', 'O'), 'Ú', 'U'), 'ú', 'U')
                ) AS categoria,
                TRIM(prod_desc) AS descricao,
                prod_atv AS ativo
            FROM produtos_base
            WHERE prod_atv = 1
        ");

        DB::statement("
            CREATE VIEW view_precos_processados AS
            SELECT 
                UPPER(TRIM(prc_cod_prod)) AS codigo_produto,
                CASE 
                    WHEN LOWER(TRIM(prc_valor)) LIKE '%sem preço%' OR TRIM(prc_valor) = '0' THEN NULL
                    ELSE CAST(
                        REPLACE(
                            CASE 
                                WHEN prc_valor LIKE '%,%' THEN REPLACE(REPLACE(TRIM(prc_valor), '.', ''), ',', '.')
                                ELSE REPLACE(TRIM(prc_valor), 'R$', '')
                            END, 
                        'R$', '')
                    AS REAL)
                END AS valor,
                CASE 
                    WHEN prc_promo IS NULL OR TRIM(prc_promo) = '' THEN NULL
                    ELSE CAST(
                        REPLACE(
                            CASE 
                                WHEN prc_promo LIKE '%,%' THEN REPLACE(REPLACE(TRIM(prc_promo), '.', ''), ',', '.')
                                ELSE REPLACE(TRIM(prc_promo), 'R$', '')
                            END, 
                        'R$', '')
                    AS REAL)
                END AS valor_promocional,
                LOWER(TRIM(prc_status)) AS status
            FROM precos_base
            WHERE LOWER(TRIM(prc_status)) = 'ativo'
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS view_precos_processados");
        DB::statement("DROP VIEW IF EXISTS view_produtos_processados");
    }
};

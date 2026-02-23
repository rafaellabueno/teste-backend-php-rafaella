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
        Schema::create('precos_base', function (Blueprint $table) {
            $table->integer('preco_id')->primary();
            $table->string('prc_cod_prod', 30)->nullable();
            $table->text('prc_valor')->nullable();
            $table->string('prc_moeda', 10)->nullable();
            $table->text('prc_desc')->nullable();
            $table->text('prc_acres')->nullable();
            $table->text('prc_promo')->nullable();
            $table->text('prc_dt_ini_promo')->nullable();
            $table->text('prc_dt_fim_promo')->nullable();
            $table->text('prc_dt_atual')->nullable();
            $table->string('prc_origem', 50)->nullable();
            $table->string('prc_tipo_cli', 30)->nullable();
            $table->string('prc_vend_resp', 100)->nullable();
            $table->text('prc_obs')->nullable();
            $table->string('prc_status', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precos_base');
    }
};

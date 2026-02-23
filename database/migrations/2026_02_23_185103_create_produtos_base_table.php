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
        Schema::create('produtos_base', function ($table) {
        $table->integer('prod_id')->primary(); 
        $table->string('prod_cod', 30)->nullable();
        $table->string('prod_nome', 150)->nullable();
        $table->string('prod_cat', 50)->nullable();
        $table->string('prod_subcat', 50)->nullable();
        $table->text('prod_desc')->nullable();
        $table->string('prod_fab', 100)->nullable();
        $table->string('prod_mod', 50)->nullable();
        $table->string('prod_cor', 30)->nullable();
        $table->text('prod_peso')->nullable();
        $table->text('prod_larg')->nullable();
        $table->text('prod_alt')->nullable();
        $table->text('prod_prof')->nullable();
        $table->string('prod_und', 10)->nullable();
        $table->boolean('prod_atv')->default(true);
        $table->text('prod_dt_cad')->nullable();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produtos_base');
    }
};

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
        Schema::create('preco_insercao', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_produto');
            $table->decimal('valor', 10, 2);
            $table->decimal('valor_promocional', 10, 2)->nullable();
            $table->string('status');
            $table->timestamps();
            $table->foreign('codigo_produto')->references('codigo')->on('produto_insercao')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preco_insercao');
    }
};

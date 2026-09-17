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
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            // CPF ou CNPJ. Sem validação de formato aqui — isso fica por conta
            // do request/validation layer.
            $table->string('document');
            $table->string('email');
            $table->string('status', 20)->default('active');
            $table->timestamps();

            // Buscas por documento e checagem de duplicidade na camada de aplicação.
            $table->index('document');

            // Listagem de clientes filtrada por status (active/inactive).
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

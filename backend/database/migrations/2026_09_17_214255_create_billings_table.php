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
        Schema::create('billings', function (Blueprint $table) {
            $table->id();

            // onDelete restrict: um cliente com cobranças nunca pode ser apagado,
            // preservando o histórico financeiro.
            $table->foreignId('customer_id')
                ->constrained()
                ->onDelete('restrict');

            $table->string('description');

            // Dinheiro sempre em decimal, nunca float, para evitar erro de
            // arredondamento binário.
            $table->decimal('original_amount', 12, 2);

            $table->date('issue_date');
            $table->date('due_date');
            $table->date('payment_date')->nullable();

            // Percentual, ex: 2.50 = 2,5% ao mês.
            $table->decimal('monthly_interest_rate', 5, 2);

            $table->string('status', 20)->default('pending');

            // Preenchidos só no momento do registro do pagamento — nunca
            // recalculados depois disso (juros param na data de pagamento).
            $table->decimal('paid_amount', 12, 2)->nullable();
            $table->decimal('interest_amount_at_payment', 12, 2)->nullable();

            $table->timestamps();

            // customer_id: índice já criado por constrained() (chave estrangeira).

            // (status, due_date): filtro mais comum do sistema — cobranças vencidas
            // e não pagas (status <> 'paid' AND due_date < hoje), base do cálculo
            // de juros em tempo real e da tela de cobrança. status primeiro porque
            // é sempre usado com igualdade/IN (baixa cardinalidade, alta seletividade
            // logo de cara); due_date entra depois para o range scan dentro do
            // bloco do índice já restrito por status.
            $table->index(['status', 'due_date']);

            // (status, issue_date): relatório de faturamento filtrando por período
            // de emissão + status. Mesmo raciocínio: igualdade (status) antes do
            // range (issue_date).
            $table->index(['status', 'issue_date']);

            // (status, payment_date): relatório de faturamento filtrando por período
            // de pagamento + status (ex: "quanto foi recebido entre X e Y").
            $table->index(['status', 'payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('billings');
    }
};

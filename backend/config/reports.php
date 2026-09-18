<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Limite de linhas para exportação em PDF
    |--------------------------------------------------------------------------
    |
    | PDF é inerentemente mais custoso de renderizar do que CSV — e não é só
    | teoria: medido neste projeto (dompdf, tabela de 10 colunas), o consumo
    | de memória escala de forma bem pior que linear com o número de linhas:
    |
    |    100 linhas  ~76 MB   |  1.000 linhas ~499 MB
    |    300 linhas ~137 MB   |  2.000 linhas  ESTOURA mesmo com limite de 1 GB
    |    500 linhas ~223 MB   |
    |
    | 500 foi escolhido (não os "ex: 5000" só ilustrativos do enunciado)
    | porque é o maior valor testado com margem confortável dentro do
    | memory_limit de 512M elevado especificamente para esta rota (ver
    | BillingReportExportController::pdf()) — 1.000 linhas já fica perto
    | demais do limite para ter margem seguindo variações de conteúdo
    | (nomes/descrições mais longos que os dados de teste). Acima deste
    | limite, GET /api/reports/billing/export/pdf retorna 422 sugerindo CSV
    | (que não tem esse problema — streaming linha a linha, sem esse custo
    | de layout de página). Ver README para mais detalhes.
    |
    | Exposto via config (não uma constante de classe) de propósito: os testes
    | de "acima do limite" precisam poder baixar esse valor via
    | config(['reports.pdf_row_limit' => N]) para exercitar o caminho de 422
    | sem precisar gerar milhares de registros reais.
    |
    */
    'pdf_row_limit' => (int) env('REPORTS_PDF_ROW_LIMIT', 500),

];

# Teste Técnico — Desenvolvedor Fullstack

## Objetivo

Desenvolver uma aplicação simples de faturamento com autenticação e um módulo de relatórios preparado para trabalhar com grandes volumes de dados.

O objetivo do teste é avaliar organização de código, modelagem de banco de dados, performance, domínio de backend e frontend, aplicação de regras de negócio e capacidade de justificar decisões técnicas.

---

## Tecnologias obrigatórias

### Backend

* PHP
* Laravel

### Frontend

* React ou Next.js
* TypeScript

### Banco de dados

* MySQL

### Infraestrutura

* Docker
* Docker Compose

---

## Contexto do projeto

A aplicação será utilizada para registrar cobranças realizadas para clientes.

Cada cobrança deverá possuir informações como:

* Cliente
* Descrição
* Valor original
* Data de emissão
* Data de vencimento
* Data de pagamento
* Status
* Taxa de juros
* Valor final atualizado

O sistema deverá possuir autenticação. Apenas usuários autenticados poderão acessar os registros e os relatórios.

---

## Funcionalidades obrigatórias

### 1. Autenticação

O sistema deverá permitir:

* Login
* Logout
* Proteção das rotas do sistema
* Proteção dos endpoints da API

Não é necessário desenvolver cadastro público de usuários.

---

### 2. Gestão de clientes

O sistema deverá permitir:

* Cadastrar clientes
* Editar clientes
* Listar clientes
* Visualizar os dados de um cliente

Dados mínimos do cliente:

* Nome
* Documento
* E-mail
* Status

---

### 3. Gestão de cobranças

O sistema deverá permitir:

* Cadastrar cobranças
* Editar cobranças
* Listar cobranças
* Visualizar uma cobrança
* Registrar o pagamento de uma cobrança

Dados mínimos da cobrança:

* Cliente
* Descrição
* Valor original
* Data de emissão
* Data de vencimento
* Data de pagamento
* Taxa de juros mensal
* Status

---

## Regra de negócio — cálculo de juros

Quando uma cobrança estiver vencida e ainda não estiver paga, o sistema deverá calcular seu valor atualizado em tempo real.

O cálculo deverá considerar:

* Valor original
* Taxa de juros mensal da cobrança
* Quantidade de dias em atraso
* Data atual

O candidato poderá escolher entre juros simples ou juros compostos, desde que:

* A regra utilizada esteja documentada
* O cálculo seja realizado no backend
* O resultado seja consistente em todas as telas e relatórios
* O valor calculado não precise obrigatoriamente ser salvo no banco de dados

Exemplo utilizando juros compostos:

```text
valor_atualizado = valor_original × (1 + taxa_mensal) ^ (dias_em_atraso / 30)
```

Ao registrar o pagamento, o sistema deverá armazenar:

* Data do pagamento
* Valor efetivamente pago
* Valor dos juros no momento do pagamento

---

## Módulo de relatórios

Desenvolver um relatório de faturamento por período.

O relatório deverá permitir filtros por:

* Data inicial
* Data final
* Cliente
* Status da cobrança

O usuário deverá conseguir escolher se o período será baseado em:

* Data de emissão
* Data de vencimento
* Data de pagamento

O relatório deverá exibir:

* Cliente
* Descrição da cobrança
* Data de emissão
* Data de vencimento
* Status
* Valor original
* Juros calculados
* Valor atualizado
* Valor pago

Também deverão ser exibidos totalizadores:

* Quantidade de cobranças
* Valor original total
* Total de juros
* Valor atualizado total
* Valor total recebido
* Valor total pendente

---

## Exportação dos relatórios

O relatório deverá poder ser exportado nos seguintes formatos:

* PDF
* CSV

As exportações deverão respeitar os filtros aplicados pelo usuário.

O arquivo exportado deverá conter:

* Período selecionado
* Filtros utilizados
* Dados do relatório
* Totalizadores

A solução adotada para geração dos relatórios deverá ser definida pelo candidato.

---

## Requisitos de performance

O módulo de relatórios deverá ser projetado considerando tabelas com milhões de registros.

A aplicação não precisa incluir milhões de registros no repositório, mas deverá possuir uma forma de gerar dados para testes.

Requisitos obrigatórios:

* Paginação realizada no backend
* Filtros realizados no banco de dados
* Ordenação realizada no backend
* Não carregar todos os registros em memória
* Evitar consultas N+1
* Criar índices adequados no banco de dados
* Utilizar migrations
* Disponibilizar factories ou seeders para gerar um volume significativo de dados
* Garantir que a exportação dos relatórios seja preparada para grandes volumes

O candidato deverá explicar no README:

* Quais índices foram criados
* Por que esses índices foram escolhidos
* Como o relatório se comportaria com milhões de registros
* Como a exportação em PDF e CSV se comportaria com grandes volumes
* Quais melhorias adicionais poderiam ser aplicadas em produção

---

## Frontend

O frontend deverá possuir, no mínimo:

* Tela de login
* Listagem de clientes
* Cadastro e edição de clientes
* Listagem de cobranças
* Cadastro e edição de cobranças
* Tela do relatório de faturamento
* Filtros do relatório
* Paginação
* Ordenação
* Exportação em PDF
* Exportação em CSV
* Estados de carregamento
* Tratamento de erros
* Feedback de operações realizadas com sucesso

A interface não precisa possuir um design avançado, mas deverá ser organizada, responsiva e componentizada.

---

## API

A comunicação entre frontend e backend deverá ocorrer por API.

A API deverá possuir:

* Validação das requisições
* Respostas HTTP adequadas
* Tratamento de erros
* Autenticação
* Paginação
* Filtros
* Ordenação
* Geração de relatórios em PDF
* Geração de relatórios em CSV

A estrutura e o padrão dos endpoints ficam a critério do candidato.

---

## Dockerização

O projeto deverá ser completamente executável por Docker.

A estrutura deverá incluir, no mínimo:

* Serviço do backend
* Serviço do frontend
* Serviço do MySQL
* Arquivo `docker-compose.yml`
* Configurações necessárias para comunicação entre os serviços
* Persistência dos dados do banco
* Instruções para subir o ambiente

O projeto deverá poder ser iniciado com poucos comandos, sem necessidade de configurar manualmente PHP, Node.js ou MySQL na máquina local.

---

## Testes automatizados

O projeto deverá possuir testes automatizados no backend.

Cenários mínimos:

* Usuário não autenticado não acessa o relatório
* Usuário não autenticado não exporta relatórios
* Cálculo de juros para cobrança vencida
* Cobrança paga não continua acumulando juros
* Filtros do relatório
* Totalizadores do relatório
* Registro de pagamento
* Exportação do relatório em PDF
* Exportação do relatório em CSV

Testes no frontend serão considerados um diferencial.

---

## Uso de inteligência artificial

O uso de ferramentas de inteligência artificial durante o desenvolvimento é permitido, mas não obrigatório.

Caso sejam utilizadas ferramentas de IA, as configurações, instruções ou arquivos utilizados para orientar os agentes deverão ser mantidos dentro do repositório do projeto.

Também será avaliada a forma como o candidato utiliza e configura agentes de IA no processo de desenvolvimento.

Boas práticas no uso serão consideradas de forma positiva.

---

## Organização dos commits

O desenvolvimento deverá ser realizado com commits pequenos, semânticos e separados por responsabilidade.

Evite concentrar toda a implementação em poucos commits grandes.

Exemplos:

```text
feat: add authentication structure
feat: create customers module
feat: create billing module
feat: add overdue interest calculation
feat: create billing report filters
feat: add csv report export
feat: add pdf report export
test: add billing interest tests
chore: add docker environment
docs: update project instructions
```

Os commits também serão considerados durante a avaliação.

---

## Entrega

O candidato deverá realizar a entrega seguindo obrigatoriamente este fluxo:

1. Criar um **fork** do repositório disponibilizado para o teste.
2. Criar uma nova branch dentro do fork utilizando o próprio nome.

Exemplo:

```text
joao-silva
```

3. Desenvolver toda a solução nessa branch.
4. Manter o histórico de commits pequenos, semânticos e separados por responsabilidade.
5. Ao finalizar, abrir um **Pull Request da branch criada no fork para o repositório original do teste**.

Exemplo do fluxo:

```text
fork-do-candidato:joao-silva
    ↓
repositorio-original:main
```

O Pull Request deverá conter:

* Título claro e objetivo
* Resumo da solução desenvolvida
* Instruções para executar o projeto
* Instruções para executar os testes
* Explicação das decisões técnicas
* Explicação da estratégia de performance
* Explicação da geração dos relatórios
* Pontos que não foram concluídos, caso existam

O repositório deverá conter:

* Código do backend
* Código do frontend
* Dockerfiles
* Arquivo `docker-compose.yml`
* Migrations
* Factories e seeders
* Testes automatizados
* Arquivo `.env.example`
* Instruções para executar o projeto
* Instruções para executar os testes
* Explicação das decisões técnicas
* Explicação da estratégia de performance

Não serão aceitas entregas por arquivo compactado, e-mail, link para outro repositório ou qualquer outro meio externo.

A entrega deverá ser realizada exclusivamente por meio do Pull Request aberto a partir do fork do candidato para o repositório original disponibilizado para o teste.

---

## Critérios de avaliação

Serão avaliados:

* Organização e legibilidade do código
* Arquitetura da aplicação
* Modelagem do banco de dados
* Qualidade da API
* Componentização do frontend
* Uso correto do TypeScript
* Aplicação da regra de negócio
* Performance das consultas
* Estratégia de geração dos relatórios
* Segurança e autenticação
* Dockerização do projeto
* Qualidade dos testes automatizados
* Tratamento de erros
* Documentação
* Histórico de commits
* Qualidade e organização do Pull Request
* Configuração e uso de agentes de IA, caso utilizados

---

## Diferenciais

Serão considerados diferenciais:

* Testes no frontend
* Controle de acesso por perfil
* Documentação da API
* Uso de ferramentas de análise de consultas
* Estratégia para geração de relatórios muito grandes
* Cache de relatórios ou totalizadores
* Pipeline de integração contínua
* Monitoramento ou observabilidade
* Cobertura de testes documentada

---

## Prazo sugerido

Prazo de entrega sugerido: até 5 dias corridos.

O teste foi planejado para exigir aproximadamente 8 a 12 horas de desenvolvimento.

Não é necessário implementar funcionalidades além das solicitadas. O foco deve estar na qualidade da solução, nas decisões técnicas e na clareza da implementação.


## Como executar o projeto

\`\`\`bash
cp .env.example .env
cp backend/.env.example backend/.env
docker compose up -d --build
docker compose exec backend php artisan migrate
\`\`\`

Backend: http://localhost:8000
Frontend: http://localhost:3000

## Geração de dados de volume

Comando: `php artisan db:seed:volume --count=500000`
Estratégia: inserts em lote (chunks de 1000) via DB::table()->insert(), sem
instanciar models Eloquent por registro — evita overhead de eventos/observers
e viabiliza a geração de centenas de milhares de registros em segundos.

## Relatório de faturamento — total de juros (decisão técnica)

Juros não é uma coluna persistida: para uma cobrança vencida e não paga, o
valor atualizado depende de "hoje" e é calculado sob demanda
(`InterestCalculatorService`). Isso é trivial por linha, mas o relatório
também precisa de um **totalizador de juros sobre todo o conjunto filtrado**
(não só a página exibida) — potencialmente centenas de milhares/milhões de
linhas — sem carregar esse volume inteiro em memória.

**Opções avaliadas:**

1. **Loop em PHP via `chunk()`/`cursor()`**, chamando
   `InterestCalculatorService::calculate()` linha a linha e somando.
   100% idêntico ao valor exibido por linha (é literalmente a mesma
   chamada), mas o tempo de resposta escala linearmente com o volume: para
   um filtro amplo (ex: "todas as cobranças", sem período), isso lê da
   tabela inteira, linha por linha, para o PHP.
2. **Expressão SQL replicando a fórmula composta, agregada com `SUM()` numa
   única query** (`ROUND(original_amount * POWER(1 + taxa/100, dias/30), 2)
   - original_amount`, dentro de um `CASE` por status). O banco resolve tudo
   internamente — o tempo de resposta deixa de ser proporcional ao número de
   linhas e passa a depender só da seletividade dos índices já existentes.
   Trade-off: é uma segunda implementação da fórmula (em SQL), com risco de
   divergir do `InterestCalculatorService` se a regra mudar.
3. **Pré-computar/cachear o total** (coluna materializada, job agendado).
   Rejeitada: juros muda todo dia, para toda cobrança vencida, mesmo sem
   nenhuma escrita — qualquer cache ficaria desatualizado diariamente,
   contrariando o requisito de "juros sempre em tempo real, nunca
   persistido como verdade".

**Escolhida: opção 2.** Este projeto prioriza explicitamente performance em
escala (requisito de "tabelas com milhões de registros") sobre garantia
absoluta de paridade ao centavo numa tela de relatório específica. Mitigação
do risco de divergência:

- A expressão SQL replica a mesma sequência de arredondamento do PHP
  (arredonda o valor atualizado primeiro, juros = atualizado − original
  depois — nunca arredondando os dois lados separadamente), então os dois
  caminhos batem ao centavo na esmagadora maioria dos casos.
- Cobranças **pagas** não usam a fórmula em SQL — o total nesse caso soma
  `interest_amount_at_payment` (snapshot histórico já persistido), que é
  exato, sem aproximação nenhuma.
- Toda a duplicação fica isolada em um único método privado
  (`BillingReportService::liveInterestSql()`), não espalhada pelo código.
- Coberto por teste comparando o total retornado pela API com um cálculo
  manual esperado (`BillingReportControllerTest::test_totals_match_manual_calculation_for_known_records`).

Se exatidão ao centavo contra auditoria virar um requisito inegociável, a
opção 1 deve substituir esta, aceitando o custo de performance em filtros
muito amplos.

**Números medidos** (MySQL, 550.000 cobranças, 2.000 clientes — ambiente
Docker local):

- Totalizadores sobre as 550k linhas sem nenhum filtro (pior caso): **~0,53s**.
- Totalizadores + página paginada juntos: **~1,0s**, pico de memória **~38,5 MB**
  (não escala com o volume — não há hidratação de model para o agregado).
- `EXPLAIN` de uma query filtrada por `status + due_date` confirma uso do
  índice composto `billings_status_due_date_index` (`type: range`,
  `Extra: Using index`), não table scan.
- O tempo de resposta HTTP fim-a-fim observado (~4–9s) é dominado pelo boot
  do `php artisan serve` a cada request (mesmo um endpoint trivial como
  `/api/me` leva ~3,3s) — não pelo relatório em si. Em produção (PHP-FPM ou
  Octane, sem reboot do framework por requisição), esse overhead fixo
  desaparece e o tempo de resposta tende ao custo medido acima (~1s).

## Exportação do relatório — CSV e PDF

Ambas reaproveitam `BillingReportService::filteredQuery()` (mesmo filtro da
tela) e `::totals()` (mesmos totalizadores) — o endpoint de listagem, o CSV e
o PDF nunca podem mostrar números diferentes para o mesmo filtro, porque é
literalmente a mesma fonte.

### CSV — streaming, nunca carrega tudo em memória

`GET /api/reports/billing/export/csv` usa `response()->streamDownload()`
escrevendo direto em `fopen('php://output', 'w')` via `fputcsv()`, percorrendo
a query filtrada com `lazyById(1000)` (chunks por id, com eager load de
`customer` preservado — ao contrário de `cursor()`, que não teria dado para
usar aqui sem reintroduzir N+1). Nunca existe um array com todas as linhas
em memória.

**Conteúdo do arquivo**: escolhemos linhas de metadata (título, período,
filtros aplicados, data de geração) no topo do próprio CSV, seguidas de uma
linha em branco, cabeçalho da tabela, dados, outra linha em branco e uma
seção "Totalizadores" ao final — tudo em um único arquivo, sem precisar de
múltiplas abas/arquivos (CSV não suporta isso de qualquer forma). O trade-off
é que uma ferramenta que espera "primeira linha = cabeçalho" precisa pular as
5 linhas de metadata antes de importar como tabela pura — convenção comum em
exports financeiros (extratos bancários costumam fazer o mesmo). Nome do
arquivo reflete o período filtrado (`relatorio-faturamento_2025-01-01_a_2025-06-30.csv`)
ou a data de geração quando não há filtro de período.

**Números medidos** (MySQL, filtro `status=overdue`, 109.718 registros —
bem acima dos "50.000+" pedidos):

- **Isolado** (só a geração do CSV, sem o boot do framework nem I/O de rede —
  `BillingReportExportService::writeCsv()` chamado diretamente): **~10,8s**,
  pico de memória **~81 MB**. Memória não escala com o volume — é sempre o
  tamanho de alguns chunks, nunca o dataset inteiro.
- **HTTP fim-a-fim** (`php artisan serve`, mesma máquina): **~19s**. A
  diferença (~8s) foi investigada, não só aceita: descartei rede Docker (um
  arquivo estático de 13MB pelo mesmo túnel de porta baixa em 0,4s) e
  descartei NAT host↔container (rodar o mesmo curl de *dentro* do container
  deu o mesmo ~18s). Sobra o próprio `php artisan serve` — servidor de
  desenvolvimento single-threaded, explicitamente não recomendado para
  produção, com overhead conhecido em respostas longas/streamed. Ajustar o
  intervalo de `flush()` de 1000 para 5000 linhas (menos idas à rede) já
  recuperou ~4s sozinho. Em produção (PHP-FPM/nginx ou Octane), o número
  isolado (~11s) é a estimativa mais realista.
- Achado real ao medir: **cada chamada de `fputcsv()` sem o 5º parâmetro
  (`$escape`) emite um deprecation warning no PHP 8.4**. Isso passou
  despercebido até o export de ~11 mil linhas ter demorado 19s por causa só
  disso — corrigido passando `$escape` explicitamente em todas as chamadas
  (`writeCsvRow()`), o que sozinho derrubou esse caso de 19s para ~1,4s.

### PDF — trava de volume por COUNT, nunca por tentativa e erro

`GET /api/reports/billing/export/pdf` primeiro roda um `COUNT(*)` (nunca
carrega os registros) sobre a query filtrada. Se o total exceder
`config('reports.pdf_row_limit')` (default **500**, `REPORTS_PDF_ROW_LIMIT`
no `.env`), retorna 422 com uma mensagem explicando o limite e sugerindo CSV
— sem nunca chegar a instanciar o dompdf.

**Por que 500, e não os "ex: 5000" do enunciado (só ilustrativo):** medimos.
dompdf (biblioteca instalada: `barryvdh/laravel-dompdf`) escala memória de
forma muito pior que linear para tabelas grandes:

| Linhas | Pico de memória |
|-------:|----------------:|
|    100 |          ~76 MB |
|    300 |         ~137 MB |
|    500 |         ~223 MB |
|  1.000 |         ~499 MB |
|  2.000 | **estoura mesmo com limite de 1 GB** |

500 é o maior valor testado com margem confortável dentro dos 512 MB de
`memory_limit` que a rota eleva especificamente para si mesma (só para essa
requisição pontual e sempre bounded — nunca o processo inteiro nem outras
requisições, ver `BillingReportExportController::pdf()`). 1.000 já fica
perto demais do limite para sobreviver a conteúdo real mais longo (nomes/
descrições maiores que os dados de teste). Isso confirma na prática o que o
enunciado já antecipava: PDF é inerentemente mais caro de renderizar (layout
de página, fontes, paginação) e menos útil como documento de leitura em
volumes grandes — ninguém lê um PDF de dezenas de milhares de linhas, e a
biblioteca nem sequer consegue gerar um de forma confiável. CSV é a resposta
correta para volume; PDF é para relatórios pequenos/pontuais que alguém vai
efetivamente imprimir ou anexar a um e-mail.

### Melhorias adicionais possíveis em produção

- Exportação assíncrona (job em fila + notificação/e-mail com link para
  download) para o CSV de volumes muito grandes, evitando manter uma conexão
  HTTP síncrona aberta por dezenas de segundos.
- Servir o backend via PHP-FPM/nginx ou Laravel Octane em vez de
  `artisan serve`, eliminando o overhead de boot por requisição observado
  em todos os endpoints deste projeto (não só nos relatórios).
- Cache de contagem para o limite do PDF em filtros muito repetidos (o
  `COUNT(*)` já é rápido graças aos índices compostos, mas evitar refazê-lo
  a cada tentativa de export do mesmo filtro é possível).
Tempo medido: ~500.000 cobranças em ~35-40s de trabalho real de geração.

## Máscaras de input (Cliente/Cobrança)

### Formato de persistência do documento (CPF/CNPJ)

Antes de decidir como mascarar, verifiquei o que já está gravado: todos os
registros existentes (`CustomerFactory`, `VolumeSeeder`, dados reais no MySQL
local) persistem `document` **com pontuação** (`000.625.630-92`,
`00.414.141/0001-50`) — nenhuma divergência entre as fontes. Não havia
ambiguidade real depois de checar, então o formulário mantém essa mesma
convenção: o estado do campo já é a string mascarada, é exatamente isso que
vai no `PUT`/`POST` para a API, sem um segundo formato "puro" por trás disso
(diferente de moeda/percentual, onde o valor real e a máscara são coisas
distintas — ver abaixo).

### Biblioteca vs. funções puras

Nenhuma biblioteca de máscara (react-input-mask, imask etc.) foi adicionada.
Os três casos aqui — CPF/CNPJ, moeda BRL, percentual — são padrões fixos e
simples o bastante (inserir separadores em posições calculadas a partir da
contagem de dígitos) para não justificar uma nova dependência: mais bundle,
mais uma API para aprender, e bibliotecas de máscara de input têm histórico
conhecido de bugs de posição de cursor que uma função pura não tem porque não
mexe com o DOM diretamente. Implementado como funções puras em
`frontend/lib/masks.ts` — sem estado, sem efeito colateral, testáveis sem
montar nenhum componente.

- `maskDocument(rawValue)`: detecta CPF (≤ 11 dígitos) vs CNPJ (> 11 dígitos)
  dinamicamente enquanto o usuário digita e retorna a string já formatada
  (é isso que é enviado à API, ver acima). Não valida dígito verificador —
  fora de escopo, só formatação.
- `parseTwoDecimalInput(rawValue)` + `formatCurrencyInput`/`formatPercentageInput`:
  masking "cents-first" (padrão comum em input de dinheiro BR — os dígitos
  digitados são sempre a parte decimal, ex: "1234" vira 12,34) para
  `original_amount` e `monthly_interest_rate`. O estado do componente guarda
  o **número real** (`1234.56`, não a string mascarada); a máscara é só
  como esse número é exibido no input a cada render. `formatCurrencyInput`
  reaproveita o `formatCurrency` já usado no resto do app (tabelas, relatório),
  para o campo de valor no formulário não introduzir um formato diferente do
  que já aparece em toda parte. Como o parsing extrai só dígitos com regex, é
  estruturalmente impossível digitar um caractere não numérico ou um valor
  negativo — não é uma validação a mais, é uma garantia do próprio parsing.

Aplicado em `CustomerForm` (documento) e `BillingForm` (valor original, taxa
de juros) — como as telas de cadastro/edição de cobrança compartilham o
`BillingForm`, uma única mudança cobriu `/cobrancas/nova` e
`/cobrancas/[id]/editar` ao mesmo tempo.

### Testes de frontend (setup novo)

O projeto não tinha nenhum test runner no frontend. Como as funções de
`lib/masks.ts` são puras (sem DOM, sem componente para montar), o custo de
adicionar um runner só para isso é baixo — usei Vitest (compatível com o
`@types/node` `^20` já declarado; a versão mais nova exige `>=22`, então
fixei em `^3.2.7`) rodando em ambiente `node` puro, sem jsdom/Testing Library,
porque nada aqui precisa de DOM. `npm run test` roda os 14 casos em
`lib/masks.test.ts` (mask progressivo de CPF/CNPJ dígito a dígito, transição
para CNPJ ao digitar o 12º dígito, parsing ida-e-volta com a formatação).
Isso conta como o "diferencial" de testes de frontend citado no README do
teste — decidi que valia o investimento porque o setup ficou mínimo (só
`vitest` + um config de 15 linhas) e porque testar essas funções manualmente
via browser a cada mudança seria mais lento que rodar `npm run test`.
Testar os componentes de formulário em si (render + digitação simulada) não
foi feito — exigiria jsdom/Testing Library, um passo a mais que não foi
justificado só para validar chamadas de função já cobertas isoladamente.
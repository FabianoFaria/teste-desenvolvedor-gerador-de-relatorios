# Contexto do Projeto — Gerador de Relatórios de Faturamento

Este arquivo orienta o Claude Code (e qualquer outro agente de IA) sobre as regras,
convenções e decisões deste projeto. Faz parte da entrega do teste técnico da Inffus.

## Objetivo

Aplicação de faturamento com autenticação e um módulo de relatórios preparado para
grandes volumes de dados (projetado para tabelas com milhões de registros).

## Stack

- Backend: PHP 8.3 + Laravel 11 + Sanctum (autenticação)
- Frontend: Next.js (App Router) + TypeScript
- Banco: MySQL 8
- Infra: Docker + Docker Compose
- Fila (exportações grandes): Laravel Queue (driver database)

## Regras de negócio críticas

### Cálculo de juros
- Cobrança vencida e não paga: valor atualizado calculado em tempo real, NUNCA
  persistido como "verdade" no banco. Só valor pago e juros no momento do pagamento
  são persistidos ao registrar o pagamento.
- Fórmula (juros compostos):
  `valor_atualizado = valor_original * (1 + taxa_mensal) ^ (dias_em_atraso / 30)`
- Centralizada em um único Service (ex: `App\Services\InterestCalculatorService`) e
  reutilizada em: listagem, tela de cobrança, relatório e exportações. Nunca duplicar
  a fórmula em mais de um lugar.
- Cobrança paga não continua acumulando juros — o cálculo para na data de pagamento.

### Relatório de faturamento
- Filtros: data inicial, data final, cliente, status.
- Período baseado em emissão, vencimento OU pagamento (usuário escolhe).
- Totalizadores: quantidade, valor original total, total de juros, valor atualizado
  total, total recebido, total pendente.
- Agregações resolvidas no banco (SQL), nunca em loop PHP sobre coleção carregada.

## Convenções de commit

Conventional Commits, pequenos, por responsabilidade:
- feat: nova funcionalidade
- fix: correção
- chore: infraestrutura/configuração
- docs: documentação
- test: testes

## Padrões de performance (obrigatórios, avaliados)

- Paginação sempre no backend.
- Filtros e ordenação resolvidos via query no banco.
- Eager loading (`with()`) obrigatório — evitar N+1.
- Índices compostos alinhados aos filtros do relatório (documentar em
  `docs/performance.md` conforme forem criados).
- CSV exportado via stream; PDF em fila/job assíncrono quando o volume for grande.

## Testes obrigatórios (cenários mínimos do teste)

- Usuário não autenticado não acessa relatório nem exportações.
- Cálculo de juros para cobrança vencida.
- Cobrança paga não acumula juros.
- Filtros e totalizadores do relatório.
- Registro de pagamento.
- Exportação em PDF e CSV.

## Como o Claude deve ajudar neste projeto

- Priorizar sugestões alinhadas aos padrões de performance acima.
- Ao propor decisão arquitetural, explicar o raciocínio — o texto é reaproveitado
  na documentação do PR.
- Apontar ambiguidades de regra de negócio em vez de decidir sozinho.
- Seguir a convenção de commits ao sugerir mensagens.

## Autenticação

- Sanctum em modo token (Bearer), não cookie/SPA — o frontend Next.js roda em
  domínio/porta separada do backend, então token é mais simples e direto que
  CSRF+cookie cross-origin.
- Não há cadastro público de usuário (fora do escopo do teste). Usuário de teste
  é criado via `UserSeeder` (ver README para credenciais).
- Token enviado pelo frontend via header `Authorization: Bearer <token>`,
  armazenado em memória/estado do client (não em localStorage, para reduzir
  exposição a XSS — avaliar httpOnly cookie como melhoria futura).

## Convenção de UI (frontend)

- Tailwind CSS + shadcn/ui como biblioteca de componentes base.
- Tema claro, paleta neutra (slate/zinc) com uma cor de destaque única para
  ações primárias (botões, links ativos).
- Layout autenticado: sidebar fixa (navegação) + topbar (usuário logado/logout)
  + área de conteúdo central.
- Sem tela de registro de usuário — apenas login.
- Prioridade: organização, responsividade e componentização sobre refinamento
  visual (conforme README do teste). Componentes reutilizáveis para: tabela
  paginada, formulário de filtro, card de totalizador, feedback de
  sucesso/erro (toast).

## Decisões adiadas (revisar se sobrar tempo)

- Feedback de erro/sucesso: banners inline usados no login (MVP). Ao implementar
  cadastro de cliente/cobrança e registro de pagamento, avaliar migrar para
  componente de toast único (sonner, via shadcn/ui) reaproveitado em todo o
  frontend — mais consistente que banners espalhados.

## Trade-off conhecido: persistência de sessão

Como o token fica apenas em memória (React state, não localStorage/cookie),
um refresh de página (F5) desloga o usuário. Essa é uma escolha deliberada
para reduzir superfície de ataque XSS. Uma melhoria futura seria usar cookie
httpOnly setado pelo backend, o que manteria a sessão através de refreshes
sem expor o token ao JavaScript do client.

## Decisão: tela de visualização de cliente

A funcionalidade "visualizar os dados de um cliente" foi implementada através
da tela de edição (que busca e exibe o registro completo), em vez de uma rota
somente-leitura separada. Justificativa: o formulário de edição já cobre a
necessidade de visualização sem duplicar UI. Uma tela /clientes/[id] read-only
separada é um possível follow-up se sobrar tempo.

## Decisão: status 'cancelled' não é alcançável via API

O schema e o InterestCalculatorService suportam status 'cancelled' (juros não
acumulam), mas não há endpoint de cancelamento — não é uma funcionalidade
pedida no escopo do teste. Implementado de forma defensiva para o caso de
uma futura extensão (ex: cancelamento manual via admin/seeder).

## Limitação conhecida: seletor de cliente em cadastro/edição de cobrança

O select de cliente carrega até 100 registros via perPage=100. Acima disso,
alguns clientes não aparecem na lista. Mitigação parcial: ao editar uma
cobrança existente, o cliente vinculado é sempre injetado nas opções mesmo
fora da página buscada. Melhoria futura: combobox assíncrono com busca
server-side (GET /api/customers?search=) via Command+Popover do shadcn/ui.

## Geração de dados de volume

Comando: `php artisan db:seed:volume --count=500000`
Estratégia: inserts em lote (chunks de 1000) via DB::table()->insert(), sem
instanciar models Eloquent por registro — evita overhead de eventos/observers
e viabiliza a geração de centenas de milhares de registros em segundos.
Tempo medido: ~500.000 cobranças em ~35-40s de trabalho real de geração.
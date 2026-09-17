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
"use client";

import { ArrowDown, ArrowUp, ArrowUpDown } from "lucide-react";
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from "@/components/ui/table";
import { BillingStatusBadge } from "@/components/billings/billing-status-badge";
import { formatCurrency, formatDateOnly } from "@/lib/format";
import type { Billing, BillingSortableColumn } from "@/lib/api/billings";

// Só as colunas exibidas nesta tabela são ofertadas como cabeçalho clicável
// — "created_at" também é uma coluna de ordenação válida no backend, mas
// não aparece nas colunas pedidas para o relatório, então não tem cabeçalho
// para clicar aqui.
const SORTABLE_COLUMNS = ["issue_date", "due_date", "status"] as const satisfies readonly BillingSortableColumn[];

type ReportSortableColumn = (typeof SORTABLE_COLUMNS)[number];

const COLUMN_LABELS: Record<ReportSortableColumn, string> = {
  issue_date: "Emissão",
  due_date: "Vencimento",
  status: "Status",
};

interface ReportTableProps {
  billings: Billing[];
  sortBy: BillingSortableColumn;
  sortDirection: "asc" | "desc";
  onSort: (column: BillingSortableColumn) => void;
}

export function ReportTable({ billings, sortBy, sortDirection, onSort }: ReportTableProps) {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Cliente</TableHead>
          <TableHead>Descrição</TableHead>
          {SORTABLE_COLUMNS.map((column) => (
            <SortableTableHead
              key={column}
              column={column}
              sortBy={sortBy}
              sortDirection={sortDirection}
              onSort={onSort}
            />
          ))}
          <TableHead className="text-right">Valor original</TableHead>
          <TableHead className="text-right">Juros</TableHead>
          <TableHead className="text-right">Valor atualizado</TableHead>
          <TableHead className="text-right">Valor pago</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {billings.length === 0 ? (
          <TableRow>
            <TableCell colSpan={9} className="text-center text-muted-foreground">
              Nenhuma cobrança encontrada para este filtro.
            </TableCell>
          </TableRow>
        ) : (
          billings.map((billing) => (
            <TableRow key={billing.id}>
              <TableCell>{billing.customer?.name ?? "—"}</TableCell>
              <TableCell>{billing.description}</TableCell>
              <TableCell>{formatDateOnly(billing.issue_date)}</TableCell>
              <TableCell>{formatDateOnly(billing.due_date)}</TableCell>
              <TableCell>
                <BillingStatusBadge status={billing.status} />
              </TableCell>
              <TableCell className="text-right tabular-nums">
                {formatCurrency(billing.original_amount)}
              </TableCell>
              <TableCell className="text-right tabular-nums">
                {formatCurrency(billing.interest_amount)}
              </TableCell>
              <TableCell className="text-right font-medium tabular-nums">
                {formatCurrency(billing.updated_amount)}
              </TableCell>
              <TableCell className="text-right tabular-nums">
                {billing.paid_amount !== null ? formatCurrency(billing.paid_amount) : "—"}
              </TableCell>
            </TableRow>
          ))
        )}
      </TableBody>
    </Table>
  );
}

interface SortableTableHeadProps {
  column: ReportSortableColumn;
  sortBy: BillingSortableColumn;
  sortDirection: "asc" | "desc";
  onSort: (column: BillingSortableColumn) => void;
}

function SortableTableHead({ column, sortBy, sortDirection, onSort }: SortableTableHeadProps) {
  const isActive = sortBy === column;
  const Icon = !isActive ? ArrowUpDown : sortDirection === "asc" ? ArrowUp : ArrowDown;

  return (
    <TableHead>
      <button
        type="button"
        onClick={() => onSort(column)}
        className="flex items-center gap-1 font-medium text-foreground hover:text-primary"
      >
        {COLUMN_LABELS[column]}
        <Icon className="size-3.5 text-muted-foreground" />
      </button>
    </TableHead>
  );
}

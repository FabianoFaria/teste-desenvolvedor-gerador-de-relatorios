"use client";

import Link from "next/link";
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
import { PayBillingButton } from "@/components/billings/pay-billing-button";
import { formatCurrency, formatDateOnly, formatDateTime } from "@/lib/format";
import type { Billing, BillingSortableColumn } from "@/lib/api/billings";

const COLUMN_LABELS: Record<BillingSortableColumn, string> = {
  issue_date: "Emissão",
  due_date: "Vencimento",
  status: "Status",
  created_at: "Criado em",
};

interface BillingTableProps {
  billings: Billing[];
  sortBy: BillingSortableColumn;
  sortDirection: "asc" | "desc";
  onSort: (column: BillingSortableColumn) => void;
  onPaid: (billing: Billing) => void;
}

export function BillingTable({ billings, sortBy, sortDirection, onSort, onPaid }: BillingTableProps) {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Cliente</TableHead>
          <TableHead>Descrição</TableHead>
          <SortableTableHead
            column="issue_date"
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={onSort}
          />
          <SortableTableHead
            column="due_date"
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={onSort}
          />
          <SortableTableHead
            column="status"
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={onSort}
          />
          <SortableTableHead
            column="created_at"
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={onSort}
          />
          <TableHead className="text-right">Valor original</TableHead>
          <TableHead className="text-right">Juros</TableHead>
          <TableHead className="text-right">Valor atualizado</TableHead>
          <TableHead className="text-right">Ações</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {billings.length === 0 ? (
          <TableRow>
            <TableCell colSpan={10} className="text-center text-muted-foreground">
              Nenhuma cobrança encontrada.
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
              <TableCell>{formatDateTime(billing.created_at)}</TableCell>
              <TableCell className="text-right tabular-nums">
                {formatCurrency(billing.original_amount)}
              </TableCell>
              <TableCell className="text-right tabular-nums">
                {formatCurrency(billing.interest_amount)}
              </TableCell>
              <TableCell className="text-right font-medium tabular-nums">
                {formatCurrency(billing.updated_amount)}
              </TableCell>
              <TableCell className="text-right">
                <div className="flex items-center justify-end gap-3">
                  <Link
                    href={`/cobrancas/${billing.id}/editar`}
                    className="text-sm font-medium text-primary hover:underline"
                  >
                    Editar
                  </Link>
                  <PayBillingButton billing={billing} onPaid={onPaid} />
                </div>
              </TableCell>
            </TableRow>
          ))
        )}
      </TableBody>
    </Table>
  );
}

interface SortableTableHeadProps {
  column: BillingSortableColumn;
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

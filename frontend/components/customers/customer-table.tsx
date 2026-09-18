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
import { Badge } from "@/components/ui/badge";
import type { Customer, CustomerSortableColumn } from "@/lib/api/customers";

const COLUMN_LABELS: Record<CustomerSortableColumn, string> = {
  name: "Nome",
  status: "Status",
  created_at: "Criado em",
};

interface CustomerTableProps {
  customers: Customer[];
  sortBy: CustomerSortableColumn;
  sortDirection: "asc" | "desc";
  onSort: (column: CustomerSortableColumn) => void;
}

export function CustomerTable({ customers, sortBy, sortDirection, onSort }: CustomerTableProps) {
  return (
    <Table>
      <TableHeader>
        <TableRow>
          <SortableTableHead
            column="name"
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={onSort}
          />
          <TableHead>Documento</TableHead>
          <TableHead>E-mail</TableHead>
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
          <TableHead className="text-right">Ações</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        {customers.length === 0 ? (
          <TableRow>
            <TableCell colSpan={6} className="text-center text-muted-foreground">
              Nenhum cliente encontrado.
            </TableCell>
          </TableRow>
        ) : (
          customers.map((customer) => (
            <TableRow key={customer.id}>
              <TableCell>{customer.name}</TableCell>
              <TableCell>{customer.document}</TableCell>
              <TableCell>{customer.email}</TableCell>
              <TableCell>
                <Badge variant={customer.status === "active" ? "default" : "secondary"}>
                  {customer.status === "active" ? "Ativo" : "Inativo"}
                </Badge>
              </TableCell>
              <TableCell>{formatDate(customer.created_at)}</TableCell>
              <TableCell className="text-right">
                <Link
                  href={`/clientes/${customer.id}/editar`}
                  className="text-sm font-medium text-primary hover:underline"
                >
                  Editar
                </Link>
              </TableCell>
            </TableRow>
          ))
        )}
      </TableBody>
    </Table>
  );
}

interface SortableTableHeadProps {
  column: CustomerSortableColumn;
  sortBy: CustomerSortableColumn;
  sortDirection: "asc" | "desc";
  onSort: (column: CustomerSortableColumn) => void;
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

function formatDate(value: string): string {
  return new Date(value).toLocaleDateString("pt-BR");
}

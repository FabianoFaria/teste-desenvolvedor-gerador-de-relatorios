"use client";

import { useEffect, useState } from "react";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { useAuth } from "@/lib/auth/auth-context";
import { listCustomers, type Customer } from "@/lib/api/customers";
import { BILLING_STATUSES, type BillingStatus } from "@/lib/api/billings";
import { REPORT_DATE_FIELDS, type ReportDateField } from "@/lib/api/reports";

const STATUS_LABELS: Record<BillingStatus, string> = {
  pending: "Pendente",
  overdue: "Vencida",
  paid: "Paga",
  cancelled: "Cancelada",
};

const DATE_FIELD_LABELS: Record<ReportDateField, string> = {
  issue_date: "Emissão",
  due_date: "Vencimento",
  payment_date: "Pagamento",
};

interface ReportFiltersProps {
  dateFrom: string;
  dateTo: string;
  dateField: ReportDateField;
  customerId: number | null;
  status: string;
  onChange: (patch: Record<string, string | null>) => void;
}

export function ReportFilters({
  dateFrom,
  dateTo,
  dateField,
  customerId,
  status,
  onChange,
}: ReportFiltersProps) {
  const { token } = useAuth();
  const [customers, setCustomers] = useState<Customer[]>([]);

  // Select simples de até 100 clientes — mesma limitação de escala já
  // documentada em billing-filters.tsx.
  useEffect(() => {
    if (!token) {
      return;
    }

    let cancelled = false;

    async function loadCustomers(authToken: string) {
      try {
        const response = await listCustomers(authToken, {
          perPage: 100,
          sortBy: "name",
          sortDirection: "asc",
        });
        if (!cancelled) setCustomers(response.data);
      } catch {
        // Filtro de cliente é conveniência: se a lista falhar em carregar,
        // o relatório continua funcionando sem esse filtro específico.
      }
    }

    loadCustomers(token);

    return () => {
      cancelled = true;
    };
  }, [token]);

  return (
    <div className="flex flex-col gap-3">
      <div className="flex flex-col gap-3 sm:flex-row sm:items-end sm:flex-wrap">
        <div className="flex flex-col gap-1.5">
          <Label htmlFor="date_from">Data inicial</Label>
          <Input
            id="date_from"
            type="date"
            value={dateFrom}
            onChange={(event) => onChange({ date_from: event.target.value || null, page: null })}
            className="w-40"
          />
        </div>

        <div className="flex flex-col gap-1.5">
          <Label htmlFor="date_to">Data final</Label>
          <Input
            id="date_to"
            type="date"
            value={dateTo}
            onChange={(event) => onChange({ date_to: event.target.value || null, page: null })}
            className="w-40"
          />
        </div>

        <div className="flex flex-col gap-1.5">
          <Label htmlFor="date_field">Base do período</Label>
          <Select
            value={dateField}
            onValueChange={(value) => onChange({ date_field: value, page: null })}
          >
            <SelectTrigger id="date_field" className="w-40">
              <SelectValue />
            </SelectTrigger>
            <SelectContent>
              {REPORT_DATE_FIELDS.map((value) => (
                <SelectItem key={value} value={value}>
                  {DATE_FIELD_LABELS[value]}
                </SelectItem>
              ))}
            </SelectContent>
          </Select>
        </div>
      </div>

      <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
        <Select
          value={customerId ? String(customerId) : "all"}
          onValueChange={(value) =>
            onChange({ customer_id: value === "all" ? null : value, page: null })
          }
        >
          <SelectTrigger className="sm:w-56">
            <SelectValue placeholder="Cliente" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todos os clientes</SelectItem>
            {customers.map((customer) => (
              <SelectItem key={customer.id} value={String(customer.id)}>
                {customer.name}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>

        <Select
          value={status || "all"}
          onValueChange={(value) => onChange({ status: value === "all" ? null : value, page: null })}
        >
          <SelectTrigger className="sm:w-44">
            <SelectValue placeholder="Status" />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="all">Todos os status</SelectItem>
            {BILLING_STATUSES.map((value) => (
              <SelectItem key={value} value={value}>
                {STATUS_LABELS[value]}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
      </div>
    </div>
  );
}

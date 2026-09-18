"use client";

import { useEffect, useState } from "react";
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

const STATUS_LABELS: Record<BillingStatus, string> = {
  pending: "Pendente",
  overdue: "Vencida",
  paid: "Paga",
  cancelled: "Cancelada",
};

interface BillingFiltersProps {
  customerId: number | null;
  status: string;
  onChange: (patch: Record<string, string | null>) => void;
}

export function BillingFilters({ customerId, status, onChange }: BillingFiltersProps) {
  const { token } = useAuth();
  const [customers, setCustomers] = useState<Customer[]>([]);

  // Select simples de até 100 clientes — ver ambiguidade sinalizada sobre
  // escala no resumo da entrega.
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
        // Filtro de cliente é conveniência, não crítico: se a lista falhar
        // em carregar, a listagem de cobranças continua funcionando sem ele.
      }
    }

    loadCustomers(token);

    return () => {
      cancelled = true;
    };
  }, [token]);

  return (
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
  );
}

"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { Skeleton } from "@/components/ui/skeleton";
import { InlineAlert } from "@/components/inline-alert";
import { CustomerForm } from "@/components/customers/customer-form";
import { useAuth } from "@/lib/auth/auth-context";
import { getCustomer, CustomerApiError, type Customer } from "@/lib/api/customers";

interface EditCustomerViewProps {
  customerId: string;
}

export function EditCustomerView({ customerId }: EditCustomerViewProps) {
  const { token } = useAuth();
  const [customer, setCustomer] = useState<Customer | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!token) {
      return;
    }

    let cancelled = false;

    async function loadCustomer(authToken: string) {
      setIsLoading(true);
      setError(null);

      try {
        const response = await getCustomer(authToken, customerId);
        if (!cancelled) {
          setCustomer(response.data);
        }
      } catch (err) {
        if (cancelled) return;
        setError(
          err instanceof CustomerApiError
            ? err.response.message
            : "Não foi possível carregar o cliente. Tente novamente."
        );
      } finally {
        if (!cancelled) setIsLoading(false);
      }
    }

    loadCustomer(token);

    return () => {
      cancelled = true;
    };
  }, [token, customerId]);

  return (
    <div className="flex flex-col gap-4">
      <div className="flex flex-col gap-1">
        <Link href="/clientes" className="text-sm text-muted-foreground hover:text-foreground">
          ← Voltar para clientes
        </Link>
        <h1 className="text-2xl font-semibold text-foreground">Editar cliente</h1>
      </div>

      {error ? (
        <InlineAlert variant="error" message={error} />
      ) : isLoading || !customer ? (
        <div className="max-w-xl space-y-3">
          <Skeleton className="h-9 w-full" />
          <Skeleton className="h-9 w-full" />
          <Skeleton className="h-9 w-full" />
          <Skeleton className="h-9 w-48" />
        </div>
      ) : (
        <div className="max-w-xl">
          <CustomerForm mode="edit" customer={customer} onSuccess={setCustomer} />
        </div>
      )}
    </div>
  );
}

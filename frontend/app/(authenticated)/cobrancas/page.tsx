"use client";

import { useCallback, useEffect, useState } from "react";
import Link from "next/link";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { InlineAlert } from "@/components/inline-alert";
import { BillingFilters } from "@/components/billings/billing-filters";
import { BillingTable } from "@/components/billings/billing-table";
import { BillingPagination } from "@/components/billings/billing-pagination";
import { useAuth } from "@/lib/auth/auth-context";
import {
  listBillings,
  isBillingStatus,
  isBillingSortableColumn,
  BillingApiError,
  type Billing,
  type BillingListMeta,
  type BillingSortableColumn,
} from "@/lib/api/billings";

export default function BillingsPage() {
  const { token } = useAuth();
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();

  const rawStatus = searchParams.get("status");
  const status = isBillingStatus(rawStatus) ? rawStatus : undefined;
  const rawCustomerId = searchParams.get("customer_id");
  const customerId = rawCustomerId ? Number(rawCustomerId) : null;
  const rawSortBy = searchParams.get("sort_by");
  const sortBy: BillingSortableColumn = isBillingSortableColumn(rawSortBy) ? rawSortBy : "due_date";
  const sortDirection = searchParams.get("sort_direction") === "asc" ? "asc" : "desc";
  const page = Number(searchParams.get("page") ?? "1") || 1;

  const [billings, setBillings] = useState<Billing[]>([]);
  const [meta, setMeta] = useState<BillingListMeta | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  const updateQuery = useCallback(
    (patch: Record<string, string | null>) => {
      const params = new URLSearchParams(searchParams.toString());

      for (const [key, value] of Object.entries(patch)) {
        if (value === null || value === "") {
          params.delete(key);
        } else {
          params.set(key, value);
        }
      }

      const queryString = params.toString();
      router.push(queryString ? `${pathname}?${queryString}` : pathname);
    },
    [pathname, router, searchParams]
  );

  useEffect(() => {
    if (!token) {
      return;
    }

    let cancelled = false;

    async function loadBillings(authToken: string) {
      setIsLoading(true);
      setError(null);

      try {
        const response = await listBillings(authToken, {
          customerId: customerId ?? undefined,
          status,
          sortBy,
          sortDirection,
          page,
        });

        if (cancelled) return;
        setBillings(response.data);
        setMeta(response.meta);
      } catch (err) {
        if (cancelled) return;
        setError(
          err instanceof BillingApiError
            ? err.response.message
            : "Não foi possível carregar as cobranças. Tente novamente."
        );
      } finally {
        if (!cancelled) setIsLoading(false);
      }
    }

    loadBillings(token);

    return () => {
      cancelled = true;
    };
  }, [token, customerId, status, sortBy, sortDirection, page]);

  function handleSort(column: BillingSortableColumn) {
    if (sortBy === column) {
      updateQuery({ sort_direction: sortDirection === "asc" ? "desc" : "asc", page: null });
    } else {
      updateQuery({ sort_by: column, sort_direction: "asc", page: null });
    }
  }

  function handlePaid(updated: Billing) {
    // A resposta de POST /pay não inclui `customer` (não é eager-loaded
    // nesse endpoint) — mescla por cima da linha existente em vez de
    // substituir, para não perder o nome do cliente já exibido na tabela.
    setBillings((current) =>
      current.map((billing) => (billing.id === updated.id ? { ...billing, ...updated } : billing))
    );
  }

  return (
    <div className="flex flex-col gap-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-semibold text-foreground">Cobranças</h1>
        <Button asChild>
          <Link href="/cobrancas/nova">Nova cobrança</Link>
        </Button>
      </div>

      <BillingFilters customerId={customerId} status={status ?? ""} onChange={updateQuery} />

      {error ? (
        <InlineAlert variant="error" message={error} />
      ) : isLoading ? (
        <div className="space-y-2">
          {Array.from({ length: 8 }).map((_, index) => (
            <Skeleton key={index} className="h-10 w-full" />
          ))}
        </div>
      ) : (
        <>
          <BillingTable
            billings={billings}
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={handleSort}
            onPaid={handlePaid}
          />
          {meta ? (
            <BillingPagination
              meta={meta}
              onPageChange={(next) => updateQuery({ page: String(next) })}
            />
          ) : null}
        </>
      )}
    </div>
  );
}

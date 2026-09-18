"use client";

import { useCallback, useEffect, useState } from "react";
import Link from "next/link";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { Button } from "@/components/ui/button";
import { Skeleton } from "@/components/ui/skeleton";
import { InlineAlert } from "@/components/inline-alert";
import { CustomerFilters } from "@/components/customers/customer-filters";
import { CustomerTable } from "@/components/customers/customer-table";
import { CustomerPagination } from "@/components/customers/customer-pagination";
import { useAuth } from "@/lib/auth/auth-context";
import {
  listCustomers,
  isCustomerStatus,
  isCustomerSortableColumn,
  CustomerApiError,
  type Customer,
  type CustomerListMeta,
  type CustomerSortableColumn,
} from "@/lib/api/customers";

export default function CustomersPage() {
  const { token } = useAuth();
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();

  const rawStatus = searchParams.get("status");
  const status = isCustomerStatus(rawStatus) ? rawStatus : undefined;
  const search = searchParams.get("search") ?? "";
  const rawSortBy = searchParams.get("sort_by");
  const sortBy: CustomerSortableColumn = isCustomerSortableColumn(rawSortBy)
    ? rawSortBy
    : "created_at";
  const sortDirection = searchParams.get("sort_direction") === "asc" ? "asc" : "desc";
  const page = Number(searchParams.get("page") ?? "1") || 1;

  const [customers, setCustomers] = useState<Customer[]>([]);
  const [meta, setMeta] = useState<CustomerListMeta | null>(null);
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

    async function loadCustomers(authToken: string) {
      setIsLoading(true);
      setError(null);

      try {
        const response = await listCustomers(authToken, {
          status,
          search: search || undefined,
          sortBy,
          sortDirection,
          page,
        });

        if (cancelled) return;
        setCustomers(response.data);
        setMeta(response.meta);
      } catch (err) {
        if (cancelled) return;
        setError(
          err instanceof CustomerApiError
            ? err.response.message
            : "Não foi possível carregar os clientes. Tente novamente."
        );
      } finally {
        if (!cancelled) setIsLoading(false);
      }
    }

    loadCustomers(token);

    return () => {
      cancelled = true;
    };
  }, [token, status, search, sortBy, sortDirection, page]);

  function handleSort(column: CustomerSortableColumn) {
    if (sortBy === column) {
      updateQuery({ sort_direction: sortDirection === "asc" ? "desc" : "asc", page: null });
    } else {
      updateQuery({ sort_by: column, sort_direction: "asc", page: null });
    }
  }

  return (
    <div className="flex flex-col gap-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-semibold text-foreground">Clientes</h1>
        <Button asChild>
          <Link href="/clientes/novo">Novo cliente</Link>
        </Button>
      </div>

      <CustomerFilters status={status ?? ""} search={search} onChange={updateQuery} />

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
          <CustomerTable
            customers={customers}
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={handleSort}
          />
          {meta ? (
            <CustomerPagination
              meta={meta}
              onPageChange={(next) => updateQuery({ page: String(next) })}
            />
          ) : null}
        </>
      )}
    </div>
  );
}

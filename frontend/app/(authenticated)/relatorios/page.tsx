"use client";

import { useCallback, useEffect, useState } from "react";
import { usePathname, useRouter, useSearchParams } from "next/navigation";
import { Skeleton } from "@/components/ui/skeleton";
import { InlineAlert } from "@/components/inline-alert";
import { BillingPagination } from "@/components/billings/billing-pagination";
import { ReportFilters } from "@/components/reports/report-filters";
import { ReportTotalsCard } from "@/components/reports/report-totals-card";
import { ReportTable } from "@/components/reports/report-table";
import { ReportExportButtons } from "@/components/reports/report-export-buttons";
import { useAuth } from "@/lib/auth/auth-context";
import { isBillingStatus, isBillingSortableColumn, type Billing, type BillingListMeta, type BillingSortableColumn } from "@/lib/api/billings";
import {
  getBillingReport,
  isReportDateField,
  ReportApiError,
  type ReportDateField,
  type ReportFilterParams,
  type ReportTotals,
} from "@/lib/api/reports";

export default function BillingReportPage() {
  const { token } = useAuth();
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();

  const dateFrom = searchParams.get("date_from") ?? "";
  const dateTo = searchParams.get("date_to") ?? "";
  const rawDateField = searchParams.get("date_field");
  const dateField: ReportDateField = isReportDateField(rawDateField) ? rawDateField : "due_date";
  const rawCustomerId = searchParams.get("customer_id");
  const customerId = rawCustomerId ? Number(rawCustomerId) : null;
  const rawStatus = searchParams.get("status");
  const status = isBillingStatus(rawStatus) ? rawStatus : undefined;
  const rawSortBy = searchParams.get("sort_by");
  const sortBy: BillingSortableColumn = isBillingSortableColumn(rawSortBy) ? rawSortBy : "due_date";
  const sortDirection = searchParams.get("sort_direction") === "asc" ? "asc" : "desc";
  const page = Number(searchParams.get("page") ?? "1") || 1;

  const filters: ReportFilterParams = {
    dateFrom: dateFrom || undefined,
    dateTo: dateTo || undefined,
    dateField,
    customerId: customerId ?? undefined,
    status,
  };

  const [billings, setBillings] = useState<Billing[]>([]);
  const [meta, setMeta] = useState<BillingListMeta | null>(null);
  const [totals, setTotals] = useState<ReportTotals | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [exportError, setExportError] = useState<string | null>(null);

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

    async function loadReport(authToken: string) {
      setIsLoading(true);
      setError(null);

      try {
        const response = await getBillingReport(authToken, {
          dateFrom: dateFrom || undefined,
          dateTo: dateTo || undefined,
          dateField,
          customerId: customerId ?? undefined,
          status,
          sortBy,
          sortDirection,
          page,
        });

        if (cancelled) return;
        setBillings(response.data);
        setMeta(response.meta);
        setTotals(response.totals);
      } catch (err) {
        if (cancelled) return;
        setError(
          err instanceof ReportApiError
            ? err.response.message
            : "Não foi possível carregar o relatório. Tente novamente."
        );
      } finally {
        if (!cancelled) setIsLoading(false);
      }
    }

    loadReport(token);

    return () => {
      cancelled = true;
    };
  }, [token, dateFrom, dateTo, dateField, customerId, status, sortBy, sortDirection, page]);

  function handleSort(column: BillingSortableColumn) {
    if (sortBy === column) {
      updateQuery({ sort_direction: sortDirection === "asc" ? "desc" : "asc", page: null });
    } else {
      updateQuery({ sort_by: column, sort_direction: "asc", page: null });
    }
  }

  return (
    <div className="flex flex-col gap-4">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-semibold text-foreground">Relatório de Faturamento</h1>
        <ReportExportButtons filters={filters} onExportError={setExportError} />
      </div>

      <ReportFilters
        dateFrom={dateFrom}
        dateTo={dateTo}
        dateField={dateField}
        customerId={customerId}
        status={status ?? ""}
        onChange={updateQuery}
      />

      {exportError ? <InlineAlert variant="error" message={exportError} /> : null}

      {error ? (
        <InlineAlert variant="error" message={error} />
      ) : isLoading ? (
        <div className="space-y-4">
          <Skeleton className="h-32 w-full" />
          <div className="space-y-2">
            {Array.from({ length: 8 }).map((_, index) => (
              <Skeleton key={index} className="h-10 w-full" />
            ))}
          </div>
        </div>
      ) : (
        <>
          {totals ? <ReportTotalsCard totals={totals} /> : null}

          <ReportTable
            billings={billings}
            sortBy={sortBy}
            sortDirection={sortDirection}
            onSort={handleSort}
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

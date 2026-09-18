"use client";

import Link from "next/link";
import { useEffect, useState } from "react";
import { Skeleton } from "@/components/ui/skeleton";
import { InlineAlert } from "@/components/inline-alert";
import { BillingForm } from "@/components/billings/billing-form";
import { BillingStatusBadge } from "@/components/billings/billing-status-badge";
import { BillingAmountSummary } from "@/components/billings/billing-amount-summary";
import { formatDateOnly } from "@/lib/format";
import { useAuth } from "@/lib/auth/auth-context";
import { getBilling, BillingApiError, type Billing } from "@/lib/api/billings";

interface EditBillingViewProps {
  billingId: string;
}

export function EditBillingView({ billingId }: EditBillingViewProps) {
  const { token } = useAuth();
  const [billing, setBilling] = useState<Billing | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    if (!token) {
      return;
    }

    let cancelled = false;

    async function loadBilling(authToken: string) {
      setIsLoading(true);
      setError(null);

      try {
        const response = await getBilling(authToken, billingId);
        if (!cancelled) {
          setBilling(response.data);
        }
      } catch (err) {
        if (cancelled) return;
        setError(
          err instanceof BillingApiError
            ? err.response.message
            : "Não foi possível carregar a cobrança. Tente novamente."
        );
      } finally {
        if (!cancelled) setIsLoading(false);
      }
    }

    loadBilling(token);

    return () => {
      cancelled = true;
    };
  }, [token, billingId]);

  return (
    <div className="flex flex-col gap-4">
      <div className="flex flex-col gap-1">
        <Link href="/cobrancas" className="text-sm text-muted-foreground hover:text-foreground">
          ← Voltar para cobranças
        </Link>
        <h1 className="text-2xl font-semibold text-foreground">Editar cobrança</h1>
      </div>

      {error ? (
        <InlineAlert variant="error" message={error} />
      ) : isLoading || !billing ? (
        <div className="max-w-2xl space-y-3">
          <Skeleton className="h-9 w-full" />
          <Skeleton className="h-9 w-full" />
          <Skeleton className="h-9 w-full" />
          <Skeleton className="h-9 w-48" />
        </div>
      ) : billing.status === "paid" ? (
        // Cobrança paga: não renderiza o formulário editável, nem deixa
        // chegar a tentar submeter e só então descobrir o 422 — mostra o
        // estado read-only direto, com o motivo explicado.
        <div className="max-w-2xl">
          <ReadOnlyPaidBilling billing={billing} />
        </div>
      ) : (
        <div className="max-w-2xl">
          <BillingForm mode="edit" billing={billing} onSuccess={setBilling} />
        </div>
      )}
    </div>
  );
}

function ReadOnlyPaidBilling({ billing }: { billing: Billing }) {
  return (
    <div className="flex flex-col gap-4 rounded-lg border border-border p-4">
      <InlineAlert
        variant="success"
        message="Esta cobrança já foi paga e não pode mais ser editada."
      />

      <dl className="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
          <dt className="text-xs text-muted-foreground">Cliente</dt>
          <dd className="text-sm font-medium text-foreground">{billing.customer?.name ?? "—"}</dd>
        </div>
        <div>
          <dt className="text-xs text-muted-foreground">Status</dt>
          <dd>
            <BillingStatusBadge status={billing.status} />
          </dd>
        </div>
        <div>
          <dt className="text-xs text-muted-foreground">Descrição</dt>
          <dd className="text-sm font-medium text-foreground">{billing.description}</dd>
        </div>
        <div>
          <dt className="text-xs text-muted-foreground">Data do pagamento</dt>
          <dd className="text-sm font-medium text-foreground">
            {billing.payment_date ? formatDateOnly(billing.payment_date) : "—"}
          </dd>
        </div>
        <div>
          <dt className="text-xs text-muted-foreground">Emissão</dt>
          <dd className="text-sm font-medium text-foreground">
            {formatDateOnly(billing.issue_date)}
          </dd>
        </div>
        <div>
          <dt className="text-xs text-muted-foreground">Vencimento</dt>
          <dd className="text-sm font-medium text-foreground">
            {formatDateOnly(billing.due_date)}
          </dd>
        </div>
      </dl>

      {/* Snapshot histórico do pagamento — não os campos calculados em tempo
          real (que a essa altura já voltam zerados pelo InterestCalculatorService
          para uma cobrança paga). */}
      <BillingAmountSummary
        originalAmount={billing.original_amount}
        interestAmount={billing.interest_amount_at_payment ?? 0}
        updatedAmount={billing.paid_amount ?? billing.original_amount}
      />
    </div>
  );
}

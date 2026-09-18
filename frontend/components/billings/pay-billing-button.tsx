"use client";

import { useState, type MouseEvent } from "react";
import { Loader2 } from "lucide-react";
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
  AlertDialogTrigger,
} from "@/components/ui/alert-dialog";
import { Button } from "@/components/ui/button";
import { InlineAlert } from "@/components/inline-alert";
import { useAuth } from "@/lib/auth/auth-context";
import { formatCurrency } from "@/lib/format";
import { payBilling, BillingApiError, type Billing } from "@/lib/api/billings";

interface PayBillingButtonProps {
  billing: Billing;
  onPaid: (billing: Billing) => void;
}

const PAYABLE_STATUSES = ["pending", "overdue"];

export function PayBillingButton({ billing, onPaid }: PayBillingButtonProps) {
  const { token } = useAuth();
  const [isOpen, setIsOpen] = useState(false);
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);

  if (!PAYABLE_STATUSES.includes(billing.status)) {
    return null;
  }

  async function handleConfirm(event: MouseEvent<HTMLButtonElement>) {
    // Radix fecha o AlertDialog automaticamente ao clicar em Action — em uma
    // ação assíncrona irreversível como essa, isso esconderia um erro de API
    // antes do usuário conseguir lê-lo. Fechamos manualmente só no sucesso.
    event.preventDefault();

    if (!token) {
      return;
    }

    setIsSubmitting(true);
    setError(null);

    try {
      const response = await payBilling(token, billing.id);
      onPaid(response.data);
      setIsOpen(false);
    } catch (err) {
      setError(
        err instanceof BillingApiError
          ? err.response.message
          : "Não foi possível registrar o pagamento. Tente novamente."
      );
    } finally {
      setIsSubmitting(false);
    }
  }

  return (
    <AlertDialog open={isOpen} onOpenChange={setIsOpen}>
      <AlertDialogTrigger asChild>
        <Button type="button" variant="outline" size="sm">
          Registrar pagamento
        </Button>
      </AlertDialogTrigger>
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Registrar pagamento?</AlertDialogTitle>
          <AlertDialogDescription>
            Isso marca a cobrança &ldquo;{billing.description}&rdquo; como paga hoje, no valor de{" "}
            {formatCurrency(billing.updated_amount)} (valor original + juros até a data). Essa
            ação não pode ser desfeita.
          </AlertDialogDescription>
        </AlertDialogHeader>

        {error ? <InlineAlert variant="error" message={error} /> : null}

        <AlertDialogFooter>
          <AlertDialogCancel disabled={isSubmitting}>Cancelar</AlertDialogCancel>
          <AlertDialogAction onClick={handleConfirm} disabled={isSubmitting}>
            {isSubmitting ? (
              <>
                <Loader2 className="animate-spin" />
                Confirmando...
              </>
            ) : (
              "Confirmar pagamento"
            )}
          </AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  );
}

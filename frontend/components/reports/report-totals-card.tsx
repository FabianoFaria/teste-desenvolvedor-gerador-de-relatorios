import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card";
import { BillingAmountSummary, AmountBlock } from "@/components/billings/billing-amount-summary";
import type { ReportTotals } from "@/lib/api/reports";

interface ReportTotalsCardProps {
  totals: ReportTotals;
}

export function ReportTotalsCard({ totals }: ReportTotalsCardProps) {
  return (
    <Card>
      <CardHeader>
        <CardTitle className="text-base">Totalizadores</CardTitle>
        <CardDescription>
          {totals.count} cobrança{totals.count === 1 ? "" : "s"} no filtro atual
        </CardDescription>
      </CardHeader>
      <CardContent className="flex flex-col gap-4">
        <BillingAmountSummary
          originalAmount={totals.original_amount_total}
          interestAmount={totals.interest_total}
          updatedAmount={totals.updated_amount_total}
        />
        <div className="grid grid-cols-2 gap-4 border-t border-border pt-4">
          <AmountBlock label="Valor total recebido" value={totals.paid_total} emphasis="strong" />
          <AmountBlock label="Valor total pendente" value={totals.pending_total} emphasis="warning" />
        </div>
      </CardContent>
    </Card>
  );
}

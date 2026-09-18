import { cn } from "@/lib/utils";
import { formatCurrency } from "@/lib/format";

interface BillingAmountSummaryProps {
  originalAmount: number;
  interestAmount: number;
  updatedAmount: number;
  className?: string;
}

// Desacoplado do tipo Billing de propósito — recebe só os 3 números, para
// também servir a totalizadores agregados da tela de relatório (não apenas
// o valor de uma única cobrança).
export function BillingAmountSummary({
  originalAmount,
  interestAmount,
  updatedAmount,
  className,
}: BillingAmountSummaryProps) {
  return (
    <div className={cn("grid grid-cols-3 gap-4 text-sm", className)}>
      <AmountBlock label="Valor original" value={originalAmount} />
      <AmountBlock label="Juros" value={interestAmount} emphasis="warning" />
      <AmountBlock label="Valor atualizado" value={updatedAmount} emphasis="strong" />
    </div>
  );
}

interface AmountBlockProps {
  label: string;
  value: number;
  emphasis?: "neutral" | "warning" | "strong";
}

function AmountBlock({ label, value, emphasis = "neutral" }: AmountBlockProps) {
  return (
    <div className="flex flex-col gap-0.5">
      <span className="text-xs text-muted-foreground">{label}</span>
      <span
        className={cn(
          "font-medium tabular-nums text-foreground",
          emphasis === "warning" && value > 0 && "text-destructive",
          emphasis === "strong" && "font-semibold"
        )}
      >
        {formatCurrency(value)}
      </span>
    </div>
  );
}

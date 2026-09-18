import { Badge } from "@/components/ui/badge";
import type { BillingStatus } from "@/lib/api/billings";

const STATUS_LABELS: Record<BillingStatus, string> = {
  pending: "Pendente",
  overdue: "Vencida",
  paid: "Paga",
  cancelled: "Cancelada",
};

const STATUS_VARIANTS: Record<BillingStatus, "default" | "secondary" | "destructive" | "outline"> = {
  pending: "secondary",
  overdue: "destructive",
  paid: "default",
  cancelled: "outline",
};

export function BillingStatusBadge({ status }: { status: BillingStatus }) {
  return <Badge variant={STATUS_VARIANTS[status]}>{STATUS_LABELS[status]}</Badge>;
}

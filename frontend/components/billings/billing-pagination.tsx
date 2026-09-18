import { Button } from "@/components/ui/button";
import type { BillingListMeta } from "@/lib/api/billings";

interface BillingPaginationProps {
  meta: BillingListMeta;
  onPageChange: (page: number) => void;
}

export function BillingPagination({ meta, onPageChange }: BillingPaginationProps) {
  if (meta.last_page <= 1) {
    return null;
  }

  const canGoPrevious = meta.current_page > 1;
  const canGoNext = meta.current_page < meta.last_page;

  return (
    <div className="flex items-center justify-between pt-2">
      <p className="text-sm text-muted-foreground">
        {meta.total} cobrança{meta.total === 1 ? "" : "s"} — página {meta.current_page} de{" "}
        {meta.last_page}
      </p>
      <div className="flex gap-2">
        <Button
          type="button"
          variant="outline"
          size="sm"
          disabled={!canGoPrevious}
          onClick={() => onPageChange(meta.current_page - 1)}
        >
          Anterior
        </Button>
        <Button
          type="button"
          variant="outline"
          size="sm"
          disabled={!canGoNext}
          onClick={() => onPageChange(meta.current_page + 1)}
        >
          Próxima
        </Button>
      </div>
    </div>
  );
}

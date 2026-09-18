"use client";

import { useState } from "react";
import { Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { useAuth } from "@/lib/auth/auth-context";
import { exportBillingReport, ReportApiError, type ReportFilterParams } from "@/lib/api/reports";

interface ReportExportButtonsProps {
  filters: ReportFilterParams;
  onExportError: (message: string) => void;
}

export function ReportExportButtons({ filters, onExportError }: ReportExportButtonsProps) {
  const { token } = useAuth();
  const [loadingFormat, setLoadingFormat] = useState<"csv" | "pdf" | null>(null);

  async function handleExport(format: "csv" | "pdf") {
    if (!token || loadingFormat) {
      return;
    }

    setLoadingFormat(format);

    try {
      await exportBillingReport(token, format, filters);
    } catch (error) {
      // O 422 de "limite de linhas excedido para PDF" já vem com uma
      // mensagem pronta do backend sugerindo CSV — mostramos ela como está,
      // sem reformular, para não divergir da explicação real do limite.
      onExportError(
        error instanceof ReportApiError
          ? error.response.message
          : "Não foi possível gerar o arquivo. Tente novamente."
      );
    } finally {
      setLoadingFormat(null);
    }
  }

  return (
    <div className="flex gap-2">
      <Button
        type="button"
        variant="outline"
        size="sm"
        disabled={loadingFormat !== null}
        onClick={() => handleExport("csv")}
      >
        {loadingFormat === "csv" ? <Loader2 className="animate-spin" /> : null}
        Exportar CSV
      </Button>
      <Button
        type="button"
        variant="outline"
        size="sm"
        disabled={loadingFormat !== null}
        onClick={() => handleExport("pdf")}
      >
        {loadingFormat === "pdf" ? <Loader2 className="animate-spin" /> : null}
        Exportar PDF
      </Button>
    </div>
  );
}

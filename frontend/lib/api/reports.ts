import { downloadBlob } from "@/lib/download";
import type { Billing, BillingListMeta, BillingSortableColumn, BillingStatus } from "@/lib/api/billings";

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";

export type ReportDateField = "issue_date" | "due_date" | "payment_date";

export const REPORT_DATE_FIELDS: ReportDateField[] = ["issue_date", "due_date", "payment_date"];

export function isReportDateField(value: string | null): value is ReportDateField {
  return value === "issue_date" || value === "due_date" || value === "payment_date";
}

/**
 * Só os filtros de conteúdo (período/cliente/status) — compartilhados entre
 * a listagem paginada e a exportação. Paginação/ordenação não fazem sentido
 * para export (a API de export sempre retorna todo o conjunto filtrado).
 */
export interface ReportFilterParams {
  dateFrom?: string;
  dateTo?: string;
  dateField?: ReportDateField;
  customerId?: number;
  status?: BillingStatus;
}

export interface ListReportParams extends ReportFilterParams {
  sortBy?: BillingSortableColumn;
  sortDirection?: "asc" | "desc";
  page?: number;
  perPage?: number;
}

export interface ReportTotals {
  count: number;
  original_amount_total: number;
  interest_total: number;
  updated_amount_total: number;
  paid_total: number;
  pending_total: number;
}

export interface ReportFiltersApplied {
  date_from: string | null;
  date_to: string | null;
  date_field: ReportDateField;
  customer_id: number | null;
  // Backend não valida o valor contra o enum de status (query inválida só
  // resulta em zero linhas) — tipar como string reflete isso com honestidade.
  status: string | null;
}

export interface BillingReportResponse {
  data: Billing[];
  meta: BillingListMeta;
  totals: ReportTotals;
  filters_applied: ReportFiltersApplied;
}

export interface ApiValidationErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}

export class ReportApiError extends Error {
  readonly status: number;
  readonly response: ApiValidationErrorResponse;

  constructor(status: number, response: ApiValidationErrorResponse) {
    super(response.message);
    this.name = "ReportApiError";
    this.status = status;
    this.response = response;
  }
}

function buildFilterQuery(filters: ReportFilterParams): URLSearchParams {
  const query = new URLSearchParams();

  if (filters.dateFrom) query.set("date_from", filters.dateFrom);
  if (filters.dateTo) query.set("date_to", filters.dateTo);
  if (filters.dateField) query.set("date_field", filters.dateField);
  if (filters.customerId) query.set("customer_id", String(filters.customerId));
  if (filters.status) query.set("status", filters.status);

  return query;
}

export async function getBillingReport(
  token: string,
  params: ListReportParams = {}
): Promise<BillingReportResponse> {
  const query = buildFilterQuery(params);

  if (params.sortBy) query.set("sort_by", params.sortBy);
  if (params.sortDirection) query.set("sort_direction", params.sortDirection);
  if (params.page) query.set("page", String(params.page));
  if (params.perPage) query.set("per_page", String(params.perPage));

  const queryString = query.toString();

  const response = await fetch(`${API_URL}/api/reports/billing${queryString ? `?${queryString}` : ""}`, {
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new ReportApiError(response.status, data as ApiValidationErrorResponse);
  }

  return data as BillingReportResponse;
}

export type ReportExportFormat = "csv" | "pdf";

/**
 * Dispara o download autenticado do relatório exportado.
 *
 * Ponto técnico: um <a href="/api/reports/billing/export/csv"> comum não
 * funciona aqui — o token vive só em memória (nunca em localStorage/cookie,
 * decisão já registrada no CLAUDE.md), e não existe forma de anexar um
 * header Authorization a uma navegação/clique de link puro. A saída é
 * buscar via fetch() (que aceita headers) pedindo o arquivo como Blob, e
 * então disparar o "Salvar como" do browser programaticamente a partir
 * desse Blob (ver lib/download.ts) — não é o fetch+Blob "por preguiça", é a
 * única forma de manter Bearer-token-em-memória E ainda assim dar ao
 * usuário uma experiência de download de arquivo normal.
 */
export async function exportBillingReport(
  token: string,
  format: ReportExportFormat,
  filters: ReportFilterParams
): Promise<void> {
  const query = buildFilterQuery(filters);
  const queryString = query.toString();
  const url = `${API_URL}/api/reports/billing/export/${format}${queryString ? `?${queryString}` : ""}`;

  const response = await fetch(url, {
    headers: {
      Accept: format === "csv" ? "text/csv" : "application/pdf",
      Authorization: `Bearer ${token}`,
    },
  });

  if (!response.ok) {
    let payload: ApiValidationErrorResponse;

    try {
      payload = (await response.json()) as ApiValidationErrorResponse;
    } catch {
      // Erros inesperados (ex: 500) podem não vir como JSON, já que pedimos
      // Accept: text/csv|application/pdf, não application/json.
      payload = { message: `Não foi possível gerar o arquivo (HTTP ${response.status}).` };
    }

    throw new ReportApiError(response.status, payload);
  }

  const blob = await response.blob();
  const filename = extractFilename(response.headers.get("Content-Disposition")) ?? `relatorio-faturamento.${format}`;

  downloadBlob(blob, filename);
}

function extractFilename(contentDisposition: string | null): string | null {
  if (!contentDisposition) {
    return null;
  }

  const match = /filename="?([^";]+)"?/.exec(contentDisposition);

  return match ? match[1] : null;
}

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";

export type BillingStatus = "pending" | "overdue" | "paid" | "cancelled";

export type BillingSortableColumn = "due_date" | "issue_date" | "status" | "created_at";

export const BILLING_STATUSES: BillingStatus[] = ["pending", "overdue", "paid", "cancelled"];

export const BILLING_SORTABLE_COLUMNS: BillingSortableColumn[] = [
  "due_date",
  "issue_date",
  "status",
  "created_at",
];

export function isBillingStatus(value: string | null): value is BillingStatus {
  return value === "pending" || value === "overdue" || value === "paid" || value === "cancelled";
}

export function isBillingSortableColumn(value: string | null): value is BillingSortableColumn {
  return (
    value === "due_date" || value === "issue_date" || value === "status" || value === "created_at"
  );
}

export interface BillingCustomerSummary {
  id: number;
  name: string;
}

export interface Billing {
  id: number;
  customer_id: number;
  // Presente em index/show (eager loaded no backend). Ausente em store/update
  // — não confiar nesse campo logo após criar/editar, refetch via getBilling
  // se precisar exibi-lo.
  customer?: BillingCustomerSummary;
  description: string;
  original_amount: number;
  issue_date: string;
  due_date: string;
  payment_date: string | null;
  monthly_interest_rate: number;
  status: BillingStatus;
  paid_amount: number | null;
  interest_amount_at_payment: number | null;
  interest_amount: number;
  updated_amount: number;
  days_overdue: number;
  created_at: string;
  updated_at: string;
}

export interface BillingListMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  path: string;
  per_page: number;
  to: number | null;
  total: number;
}

export interface BillingListLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface BillingListResponse {
  data: Billing[];
  links: BillingListLinks;
  meta: BillingListMeta;
}

export interface BillingResponse {
  data: Billing;
}

export interface BillingPayload {
  customer_id: number;
  description: string;
  original_amount: number;
  issue_date: string;
  due_date: string;
  monthly_interest_rate: number;
}

export interface ApiValidationErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}

export class BillingApiError extends Error {
  readonly status: number;
  readonly response: ApiValidationErrorResponse;

  constructor(status: number, response: ApiValidationErrorResponse) {
    super(response.message);
    this.name = "BillingApiError";
    this.status = status;
    this.response = response;
  }
}

export interface ListBillingsParams {
  customerId?: number;
  status?: BillingStatus;
  sortBy?: BillingSortableColumn;
  sortDirection?: "asc" | "desc";
  page?: number;
  perPage?: number;
}

async function request<T>(token: string, path: string, init: RequestInit = {}): Promise<T> {
  const response = await fetch(`${API_URL}${path}`, {
    ...init,
    headers: {
      Accept: "application/json",
      Authorization: `Bearer ${token}`,
      ...(init.body ? { "Content-Type": "application/json" } : {}),
      ...init.headers,
    },
  });

  const data = await response.json();

  if (!response.ok) {
    throw new BillingApiError(response.status, data as ApiValidationErrorResponse);
  }

  return data as T;
}

function buildListQuery(params: ListBillingsParams): string {
  const query = new URLSearchParams();

  if (params.customerId) query.set("customer_id", String(params.customerId));
  if (params.status) query.set("status", params.status);
  if (params.sortBy) query.set("sort_by", params.sortBy);
  if (params.sortDirection) query.set("sort_direction", params.sortDirection);
  if (params.page) query.set("page", String(params.page));
  if (params.perPage) query.set("per_page", String(params.perPage));

  const queryString = query.toString();

  return queryString ? `?${queryString}` : "";
}

export function listBillings(
  token: string,
  params: ListBillingsParams = {}
): Promise<BillingListResponse> {
  return request<BillingListResponse>(token, `/api/billings${buildListQuery(params)}`);
}

export function getBilling(token: string, id: number | string): Promise<BillingResponse> {
  return request<BillingResponse>(token, `/api/billings/${id}`);
}

export function createBilling(token: string, payload: BillingPayload): Promise<BillingResponse> {
  return request<BillingResponse>(token, "/api/billings", {
    method: "POST",
    body: JSON.stringify(payload),
  });
}

export function updateBilling(
  token: string,
  id: number | string,
  payload: BillingPayload
): Promise<BillingResponse> {
  return request<BillingResponse>(token, `/api/billings/${id}`, {
    method: "PUT",
    body: JSON.stringify(payload),
  });
}

export function payBilling(token: string, id: number | string): Promise<BillingResponse> {
  return request<BillingResponse>(token, `/api/billings/${id}/pay`, {
    method: "POST",
  });
}

const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";

export type CustomerStatus = "active" | "inactive";

export type CustomerSortableColumn = "name" | "status" | "created_at";

export const CUSTOMER_STATUSES: CustomerStatus[] = ["active", "inactive"];

export const CUSTOMER_SORTABLE_COLUMNS: CustomerSortableColumn[] = [
  "name",
  "status",
  "created_at",
];

export function isCustomerStatus(value: string | null): value is CustomerStatus {
  return value === "active" || value === "inactive";
}

export function isCustomerSortableColumn(value: string | null): value is CustomerSortableColumn {
  return value === "name" || value === "status" || value === "created_at";
}

export interface Customer {
  id: number;
  name: string;
  document: string;
  email: string;
  status: CustomerStatus;
  created_at: string;
  updated_at: string;
}

export interface CustomerListMeta {
  current_page: number;
  from: number | null;
  last_page: number;
  path: string;
  per_page: number;
  to: number | null;
  total: number;
}

export interface CustomerListLinks {
  first: string | null;
  last: string | null;
  prev: string | null;
  next: string | null;
}

export interface CustomerListResponse {
  data: Customer[];
  links: CustomerListLinks;
  meta: CustomerListMeta;
}

export interface CustomerResponse {
  data: Customer;
}

export interface CustomerPayload {
  name: string;
  document: string;
  email: string;
  status?: CustomerStatus;
}

export interface ApiValidationErrorResponse {
  message: string;
  errors?: Record<string, string[]>;
}

export class CustomerApiError extends Error {
  readonly status: number;
  readonly response: ApiValidationErrorResponse;

  constructor(status: number, response: ApiValidationErrorResponse) {
    super(response.message);
    this.name = "CustomerApiError";
    this.status = status;
    this.response = response;
  }
}

export interface ListCustomersParams {
  status?: CustomerStatus;
  search?: string;
  sortBy?: CustomerSortableColumn;
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
    throw new CustomerApiError(response.status, data as ApiValidationErrorResponse);
  }

  return data as T;
}

function buildListQuery(params: ListCustomersParams): string {
  const query = new URLSearchParams();

  if (params.status) query.set("status", params.status);
  if (params.search) query.set("search", params.search);
  if (params.sortBy) query.set("sort_by", params.sortBy);
  if (params.sortDirection) query.set("sort_direction", params.sortDirection);
  if (params.page) query.set("page", String(params.page));
  if (params.perPage) query.set("per_page", String(params.perPage));

  const queryString = query.toString();

  return queryString ? `?${queryString}` : "";
}

export function listCustomers(
  token: string,
  params: ListCustomersParams = {}
): Promise<CustomerListResponse> {
  return request<CustomerListResponse>(token, `/api/customers${buildListQuery(params)}`);
}

export function getCustomer(token: string, id: number | string): Promise<CustomerResponse> {
  return request<CustomerResponse>(token, `/api/customers/${id}`);
}

export function createCustomer(
  token: string,
  payload: CustomerPayload
): Promise<CustomerResponse> {
  return request<CustomerResponse>(token, "/api/customers", {
    method: "POST",
    body: JSON.stringify(payload),
  });
}

export function updateCustomer(
  token: string,
  id: number | string,
  payload: CustomerPayload
): Promise<CustomerResponse> {
  return request<CustomerResponse>(token, `/api/customers/${id}`, {
    method: "PUT",
    body: JSON.stringify(payload),
  });
}

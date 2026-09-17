const API_URL = process.env.NEXT_PUBLIC_API_URL ?? "http://localhost:8000";

export interface LoginPayload {
  email: string;
  password: string;
}

export interface AuthUser {
  id: number;
  name: string;
  email: string;
}

export interface LoginSuccessResponse {
  user: AuthUser;
  token: string;
}

export interface LoginErrorResponse {
  message: string;
  errors?: {
    email?: string[];
  };
}

export class LoginRequestError extends Error {
  readonly status: number;
  readonly response: LoginErrorResponse;

  constructor(status: number, response: LoginErrorResponse) {
    super(response.message);
    this.name = "LoginRequestError";
    this.status = status;
    this.response = response;
  }
}

export async function login(payload: LoginPayload): Promise<LoginSuccessResponse> {
  const response = await fetch(`${API_URL}/api/login`, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
      Accept: "application/json",
    },
    body: JSON.stringify(payload),
  });

  const data = await response.json();

  if (!response.ok) {
    throw new LoginRequestError(response.status, data as LoginErrorResponse);
  }

  return data as LoginSuccessResponse;
}

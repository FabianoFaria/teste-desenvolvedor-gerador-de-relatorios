"use client";

import {
  createContext,
  useCallback,
  useContext,
  useEffect,
  useMemo,
  useState,
  type ReactNode,
} from "react";
import type { AuthUser } from "@/lib/api/auth";

interface AuthContextValue {
  user: AuthUser | null;
  token: string | null;
  isAuthenticated: boolean;
  isLoading: boolean;
  setAuth: (user: AuthUser, token: string) => void;
  clearAuth: () => void;
}

const AuthContext = createContext<AuthContextValue | undefined>(undefined);

// Token fica só em estado de memória (nunca localStorage) para reduzir exposição a XSS,
// conforme decisão registrada no CLAUDE.md.
export function AuthProvider({ children }: { children: ReactNode }) {
  const [user, setUser] = useState<AuthUser | null>(null);
  const [token, setToken] = useState<string | null>(null);
  // Começa true para dar uma volta de hidratação antes de rotas protegidas
  // decidirem redirecionar — evita flash de redirect e dá espaço para uma
  // futura checagem assíncrona de sessão (ex: cookie httpOnly).
  const [isLoading, setIsLoading] = useState(true);

  useEffect(() => {
    let cancelled = false;

    // Hoje não há sessão para restaurar (token só existe em memória). O microtask
    // aqui é o ponto de extensão para uma futura checagem assíncrona de sessão
    // (ex: validar um cookie httpOnly contra a API) sem mudar o contrato do contexto.
    Promise.resolve().then(() => {
      if (!cancelled) {
        setIsLoading(false);
      }
    });

    return () => {
      cancelled = true;
    };
  }, []);

  const setAuth = useCallback((nextUser: AuthUser, nextToken: string) => {
    setUser(nextUser);
    setToken(nextToken);
  }, []);

  const clearAuth = useCallback(() => {
    setUser(null);
    setToken(null);
  }, []);

  const value = useMemo<AuthContextValue>(
    () => ({
      user,
      token,
      isAuthenticated: token !== null,
      isLoading,
      setAuth,
      clearAuth,
    }),
    [user, token, isLoading, setAuth, clearAuth]
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

export function useAuth(): AuthContextValue {
  const context = useContext(AuthContext);

  if (!context) {
    throw new Error("useAuth deve ser usado dentro de um AuthProvider");
  }

  return context;
}

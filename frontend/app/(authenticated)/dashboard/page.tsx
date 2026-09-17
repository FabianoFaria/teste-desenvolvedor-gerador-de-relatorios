"use client";

import { useAuth } from "@/lib/auth/auth-context";

export default function DashboardPage() {
  const { user } = useAuth();

  return (
    <div className="flex flex-col gap-2">
      <h1 className="text-2xl font-semibold text-foreground">Bem-vindo, {user?.name}</h1>
      <p className="text-sm text-muted-foreground">
        Em breve: clientes, cobranças e relatórios de faturamento.
      </p>
    </div>
  );
}

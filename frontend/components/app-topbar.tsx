"use client";

import { useAuth } from "@/lib/auth/auth-context";
import { LogoutButton } from "@/components/logout-button";

export function AppTopbar() {
  const { user } = useAuth();

  return (
    <header className="flex h-14 shrink-0 items-center justify-between border-b border-border bg-background px-6">
      <span className="text-sm font-medium text-foreground">{user?.name}</span>
      <LogoutButton />
    </header>
  );
}

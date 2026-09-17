"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import { LogOut } from "lucide-react";
import { Button } from "@/components/ui/button";
import { useAuth } from "@/lib/auth/auth-context";
import { logout } from "@/lib/api/auth";

export function LogoutButton() {
  const router = useRouter();
  const { token, clearAuth } = useAuth();
  const [isLoggingOut, setIsLoggingOut] = useState(false);

  async function handleLogout() {
    setIsLoggingOut(true);

    if (token) {
      try {
        await logout(token);
      } catch {
        // Falha de rede não deve impedir o logout local do usuário.
      }
    }

    clearAuth();
    router.replace("/login");
  }

  return (
    <Button
      type="button"
      variant="outline"
      size="sm"
      onClick={handleLogout}
      disabled={isLoggingOut}
    >
      <LogOut />
      {isLoggingOut ? "Saindo..." : "Sair"}
    </Button>
  );
}

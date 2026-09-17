"use client";

import { useRouter } from "next/navigation";
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from "@/components/ui/card";
import { LoginForm } from "@/components/login-form";
import { useAuth } from "@/lib/auth/auth-context";
import type { AuthUser } from "@/lib/api/auth";

export default function LoginPage() {
  const router = useRouter();
  const { setAuth } = useAuth();

  function handleSuccess(user: AuthUser, token: string) {
    setAuth(user, token);
    router.push("/dashboard");
  }

  return (
    <div className="flex flex-1 items-center justify-center bg-zinc-50 px-4 py-12 dark:bg-black">
      <Card className="w-full max-w-sm">
        <CardHeader>
          <CardTitle className="text-xl">Entrar</CardTitle>
          <CardDescription>
            Acesse o sistema de faturamento com suas credenciais.
          </CardDescription>
        </CardHeader>
        <CardContent>
          <LoginForm onSuccess={handleSuccess} />
        </CardContent>
      </Card>
    </div>
  );
}

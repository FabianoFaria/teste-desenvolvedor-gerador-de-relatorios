"use client";

import Link from "next/link";
import { CustomerForm } from "@/components/customers/customer-form";

export default function NewCustomerPage() {
  return (
    <div className="flex flex-col gap-4">
      <div className="flex flex-col gap-1">
        <Link href="/clientes" className="text-sm text-muted-foreground hover:text-foreground">
          ← Voltar para clientes
        </Link>
        <h1 className="text-2xl font-semibold text-foreground">Novo cliente</h1>
      </div>

      <div className="max-w-xl">
        <CustomerForm mode="create" />
      </div>
    </div>
  );
}

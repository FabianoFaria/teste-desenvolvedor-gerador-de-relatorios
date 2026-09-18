"use client";

import Link from "next/link";
import { BillingForm } from "@/components/billings/billing-form";

export default function NewBillingPage() {
  return (
    <div className="flex flex-col gap-4">
      <div className="flex flex-col gap-1">
        <Link href="/cobrancas" className="text-sm text-muted-foreground hover:text-foreground">
          ← Voltar para cobranças
        </Link>
        <h1 className="text-2xl font-semibold text-foreground">Nova cobrança</h1>
      </div>

      <div className="max-w-2xl">
        <BillingForm mode="create" />
      </div>
    </div>
  );
}

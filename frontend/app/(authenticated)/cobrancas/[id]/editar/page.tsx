import { EditBillingView } from "@/components/billings/edit-billing-view";

interface EditBillingPageProps {
  params: Promise<{ id: string }>;
}

export default async function EditBillingPage({ params }: EditBillingPageProps) {
  const { id } = await params;

  return <EditBillingView billingId={id} />;
}

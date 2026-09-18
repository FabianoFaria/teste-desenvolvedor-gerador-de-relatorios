import { EditCustomerView } from "@/components/customers/edit-customer-view";

interface EditCustomerPageProps {
  params: Promise<{ id: string }>;
}

export default async function EditCustomerPage({ params }: EditCustomerPageProps) {
  const { id } = await params;

  return <EditCustomerView customerId={id} />;
}

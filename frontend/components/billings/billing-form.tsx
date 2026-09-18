"use client";

import { useEffect, useMemo, useState, type FormEvent } from "react";
import { useRouter } from "next/navigation";
import { Loader2 } from "lucide-react";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { InlineAlert } from "@/components/inline-alert";
import { useAuth } from "@/lib/auth/auth-context";
import { listCustomers } from "@/lib/api/customers";
import {
  createBilling,
  updateBilling,
  BillingApiError,
  type Billing,
  type BillingPayload,
} from "@/lib/api/billings";

const CREATE_SUCCESS_REDIRECT_DELAY_MS = 1500;

interface CustomerOption {
  id: number;
  name: string;
}

interface FieldErrors {
  customer_id?: string;
  description?: string;
  original_amount?: string;
  issue_date?: string;
  due_date?: string;
  monthly_interest_rate?: string;
}

interface CreateBillingFormProps {
  mode: "create";
  onSuccess?: (billing: Billing) => void;
}

interface EditBillingFormProps {
  mode: "edit";
  billing: Billing;
  onSuccess?: (billing: Billing) => void;
}

type BillingFormProps = CreateBillingFormProps | EditBillingFormProps;

export function BillingForm(props: BillingFormProps) {
  const { mode } = props;
  const { token } = useAuth();
  const router = useRouter();

  const initialValues =
    mode === "edit"
      ? {
          customerId: props.billing.customer_id as number | null,
          description: props.billing.description,
          originalAmount: String(props.billing.original_amount),
          issueDate: props.billing.issue_date,
          dueDate: props.billing.due_date,
          monthlyInterestRate: String(props.billing.monthly_interest_rate),
        }
      : {
          customerId: null as number | null,
          description: "",
          originalAmount: "",
          issueDate: "",
          dueDate: "",
          monthlyInterestRate: "",
        };

  const [customerId, setCustomerId] = useState(initialValues.customerId);
  const [description, setDescription] = useState(initialValues.description);
  const [originalAmount, setOriginalAmount] = useState(initialValues.originalAmount);
  const [issueDate, setIssueDate] = useState(initialValues.issueDate);
  const [dueDate, setDueDate] = useState(initialValues.dueDate);
  const [monthlyInterestRate, setMonthlyInterestRate] = useState(
    initialValues.monthlyInterestRate
  );

  const [customerOptions, setCustomerOptions] = useState<CustomerOption[]>([]);
  const [isLoadingCustomers, setIsLoadingCustomers] = useState(true);
  const [customersError, setCustomersError] = useState<string | null>(null);

  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  useEffect(() => {
    if (!token) {
      return;
    }

    let cancelled = false;

    async function loadCustomers(authToken: string) {
      setIsLoadingCustomers(true);
      setCustomersError(null);

      try {
        const response = await listCustomers(authToken, {
          perPage: 100,
          sortBy: "name",
          sortDirection: "asc",
        });

        if (!cancelled) {
          setCustomerOptions(response.data.map((customer) => ({ id: customer.id, name: customer.name })));
        }
      } catch {
        if (!cancelled) {
          setCustomersError("Não foi possível carregar a lista de clientes.");
        }
      } finally {
        if (!cancelled) setIsLoadingCustomers(false);
      }
    }

    loadCustomers(token);

    return () => {
      cancelled = true;
    };
  }, [token]);

  // Cobrança em edição pode apontar para um cliente fora da primeira página
  // de 100 (ver ambiguidade sobre escala do select) — garante que o cliente
  // atual sempre apareça selecionado corretamente mesmo assim.
  const customerSelectOptions = useMemo(() => {
    if (mode !== "edit" || !props.billing.customer) {
      return customerOptions;
    }

    const current = props.billing.customer;
    const alreadyListed = customerOptions.some((option) => option.id === current.id);

    return alreadyListed ? customerOptions : [current, ...customerOptions];
  }, [customerOptions, mode, props]);

  function validate(): boolean {
    const nextErrors: FieldErrors = {};

    if (!customerId) {
      nextErrors.customer_id = "Selecione um cliente.";
    }

    if (!description.trim()) {
      nextErrors.description = "Informe a descrição.";
    }

    const amount = Number(originalAmount);
    if (!originalAmount.trim() || Number.isNaN(amount) || amount <= 0) {
      nextErrors.original_amount = "Informe um valor maior que zero.";
    }

    const rate = Number(monthlyInterestRate);
    if (monthlyInterestRate.trim() === "" || Number.isNaN(rate) || rate < 0) {
      nextErrors.monthly_interest_rate = "Informe uma taxa de juros válida (0 ou maior).";
    }

    if (!issueDate) {
      nextErrors.issue_date = "Informe a data de emissão.";
    }

    if (!dueDate) {
      nextErrors.due_date = "Informe a data de vencimento.";
    } else if (issueDate && dueDate < issueDate) {
      nextErrors.due_date = "O vencimento deve ser igual ou posterior à emissão.";
    }

    setFieldErrors(nextErrors);
    return Object.keys(nextErrors).length === 0;
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setFormError(null);
    setSuccessMessage(null);

    if (!token || !validate() || customerId === null) {
      return;
    }

    setIsSubmitting(true);

    try {
      const payload: BillingPayload = {
        customer_id: customerId,
        description,
        original_amount: Number(originalAmount),
        issue_date: issueDate,
        due_date: dueDate,
        monthly_interest_rate: Number(monthlyInterestRate),
      };

      const response =
        mode === "create"
          ? await createBilling(token, payload)
          : await updateBilling(token, props.billing.id, payload);

      setSuccessMessage(
        mode === "create" ? "Cobrança cadastrada com sucesso." : "Alterações salvas com sucesso."
      );
      props.onSuccess?.(response.data);
    } catch (error) {
      if (error instanceof BillingApiError) {
        setFieldErrors({
          customer_id: error.response.errors?.customer_id?.[0],
          description: error.response.errors?.description?.[0],
          original_amount: error.response.errors?.original_amount?.[0],
          issue_date: error.response.errors?.issue_date?.[0],
          due_date: error.response.errors?.due_date?.[0],
          monthly_interest_rate: error.response.errors?.monthly_interest_rate?.[0],
        });
        // "status" não é um campo deste formulário (cobrança paga é
        // bloqueada antes de chegar aqui — ver EditBillingView), mas se um
        // 422 desse tipo ocorrer mesmo assim (ex: condição de corrida com
        // outra aba pagando a cobrança nesse meio-tempo), mostra no banner
        // geral em vez de descartar silenciosamente.
        setFormError(error.response.errors?.status?.[0] ?? error.response.message);
      } else {
        setFormError("Não foi possível conectar ao servidor. Tente novamente.");
      }
    } finally {
      setIsSubmitting(false);
    }
  }

  useEffect(() => {
    if (mode !== "create" || !successMessage) {
      return;
    }

    const timeout = setTimeout(() => {
      router.push("/cobrancas");
    }, CREATE_SUCCESS_REDIRECT_DELAY_MS);

    return () => clearTimeout(timeout);
  }, [mode, successMessage, router]);

  return (
    <form onSubmit={handleSubmit} noValidate className="flex flex-col gap-4">
      <div className="flex flex-col gap-1.5">
        <Label htmlFor="customer_id">Cliente</Label>
        <Select
          value={customerId ? String(customerId) : undefined}
          onValueChange={(value) => setCustomerId(Number(value))}
          disabled={isSubmitting || isLoadingCustomers}
        >
          <SelectTrigger id="customer_id" className="w-full" aria-invalid={Boolean(fieldErrors.customer_id)}>
            <SelectValue placeholder={isLoadingCustomers ? "Carregando clientes..." : "Selecione um cliente"} />
          </SelectTrigger>
          <SelectContent>
            {customerSelectOptions.map((customer) => (
              <SelectItem key={customer.id} value={String(customer.id)}>
                {customer.name}
              </SelectItem>
            ))}
          </SelectContent>
        </Select>
        {customersError ? <p className="text-sm text-destructive">{customersError}</p> : null}
        {fieldErrors.customer_id ? (
          <p className="text-sm text-destructive">{fieldErrors.customer_id}</p>
        ) : null}
      </div>

      <div className="flex flex-col gap-1.5">
        <Label htmlFor="description">Descrição</Label>
        <Input
          id="description"
          value={description}
          onChange={(event) => setDescription(event.target.value)}
          aria-invalid={Boolean(fieldErrors.description)}
          disabled={isSubmitting}
        />
        {fieldErrors.description ? (
          <p className="text-sm text-destructive">{fieldErrors.description}</p>
        ) : null}
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div className="flex flex-col gap-1.5">
          <Label htmlFor="original_amount">Valor original</Label>
          <Input
            id="original_amount"
            type="number"
            min="0.01"
            step="0.01"
            inputMode="decimal"
            value={originalAmount}
            onChange={(event) => setOriginalAmount(event.target.value)}
            aria-invalid={Boolean(fieldErrors.original_amount)}
            disabled={isSubmitting}
          />
          {fieldErrors.original_amount ? (
            <p className="text-sm text-destructive">{fieldErrors.original_amount}</p>
          ) : null}
        </div>

        <div className="flex flex-col gap-1.5">
          <Label htmlFor="monthly_interest_rate">Taxa de juros mensal (%)</Label>
          <Input
            id="monthly_interest_rate"
            type="number"
            min="0"
            step="0.01"
            inputMode="decimal"
            value={monthlyInterestRate}
            onChange={(event) => setMonthlyInterestRate(event.target.value)}
            aria-invalid={Boolean(fieldErrors.monthly_interest_rate)}
            disabled={isSubmitting}
          />
          {fieldErrors.monthly_interest_rate ? (
            <p className="text-sm text-destructive">{fieldErrors.monthly_interest_rate}</p>
          ) : null}
        </div>
      </div>

      <div className="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div className="flex flex-col gap-1.5">
          <Label htmlFor="issue_date">Data de emissão</Label>
          <Input
            id="issue_date"
            type="date"
            value={issueDate}
            onChange={(event) => setIssueDate(event.target.value)}
            aria-invalid={Boolean(fieldErrors.issue_date)}
            disabled={isSubmitting}
          />
          {fieldErrors.issue_date ? (
            <p className="text-sm text-destructive">{fieldErrors.issue_date}</p>
          ) : null}
        </div>

        <div className="flex flex-col gap-1.5">
          <Label htmlFor="due_date">Data de vencimento</Label>
          <Input
            id="due_date"
            type="date"
            value={dueDate}
            onChange={(event) => setDueDate(event.target.value)}
            aria-invalid={Boolean(fieldErrors.due_date)}
            disabled={isSubmitting}
          />
          {fieldErrors.due_date ? (
            <p className="text-sm text-destructive">{fieldErrors.due_date}</p>
          ) : null}
        </div>
      </div>

      {formError ? <InlineAlert variant="error" message={formError} /> : null}
      {successMessage ? <InlineAlert variant="success" message={successMessage} /> : null}

      <Button type="submit" disabled={isSubmitting} className="mt-2 self-start">
        {isSubmitting ? (
          <>
            <Loader2 className="animate-spin" />
            Salvando...
          </>
        ) : mode === "create" ? (
          "Cadastrar"
        ) : (
          "Salvar alterações"
        )}
      </Button>
    </form>
  );
}

"use client";

import { useEffect, useState, type FormEvent } from "react";
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
import { maskDocument } from "@/lib/masks";
import {
  createCustomer,
  updateCustomer,
  CustomerApiError,
  type Customer,
  type CustomerStatus,
} from "@/lib/api/customers";

const EMAIL_REGEX = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
const CREATE_SUCCESS_REDIRECT_DELAY_MS = 1500;

interface FieldErrors {
  name?: string;
  document?: string;
  email?: string;
  status?: string;
}

interface CreateCustomerFormProps {
  mode: "create";
  onSuccess?: (customer: Customer) => void;
}

interface EditCustomerFormProps {
  mode: "edit";
  customer: Customer;
  onSuccess?: (customer: Customer) => void;
}

type CustomerFormProps = CreateCustomerFormProps | EditCustomerFormProps;

export function CustomerForm(props: CustomerFormProps) {
  const { mode } = props;
  const { token } = useAuth();
  const router = useRouter();

  const initialValues =
    mode === "edit"
      ? {
          name: props.customer.name,
          // Idempotente para um valor já formatado — reforça a máscara
          // mesmo num registro legado que porventura não tenha pontuação.
          document: maskDocument(props.customer.document),
          email: props.customer.email,
          status: props.customer.status,
        }
      : { name: "", document: "", email: "", status: "active" as CustomerStatus };

  const [name, setName] = useState(initialValues.name);
  const [documentNumber, setDocumentNumber] = useState(initialValues.document);
  const [email, setEmail] = useState(initialValues.email);
  const [status, setStatus] = useState<CustomerStatus>(initialValues.status);
  // Referência do status salvo no servidor — usada para só enviar `status`
  // no PUT quando o usuário de fato alterou o valor (edição preserva o
  // status atual quando o campo não é tocado, conforme contrato da API).
  const [savedStatus, setSavedStatus] = useState<CustomerStatus>(initialValues.status);

  const [fieldErrors, setFieldErrors] = useState<FieldErrors>({});
  const [formError, setFormError] = useState<string | null>(null);
  const [successMessage, setSuccessMessage] = useState<string | null>(null);
  const [isSubmitting, setIsSubmitting] = useState(false);

  function validate(): boolean {
    const nextErrors: FieldErrors = {};

    if (!name.trim()) {
      nextErrors.name = "Informe o nome.";
    }

    if (!documentNumber.trim()) {
      nextErrors.document = "Informe o documento (CPF ou CNPJ).";
    }

    if (!email.trim()) {
      nextErrors.email = "Informe o e-mail.";
    } else if (!EMAIL_REGEX.test(email)) {
      nextErrors.email = "Informe um e-mail válido.";
    }

    setFieldErrors(nextErrors);
    return Object.keys(nextErrors).length === 0;
  }

  async function handleSubmit(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setFormError(null);
    setSuccessMessage(null);

    if (!token || !validate()) {
      return;
    }

    setIsSubmitting(true);

    try {
      const statusChanged = status !== savedStatus;

      const payload = {
        name,
        document: documentNumber,
        email,
        ...(mode === "create" || statusChanged ? { status } : {}),
      };

      const response =
        mode === "create"
          ? await createCustomer(token, payload)
          : await updateCustomer(token, props.customer.id, payload);

      setSavedStatus(response.data.status);
      setSuccessMessage(
        mode === "create" ? "Cliente cadastrado com sucesso." : "Alterações salvas com sucesso."
      );

      props.onSuccess?.(response.data);
    } catch (error) {
      if (error instanceof CustomerApiError) {
        setFieldErrors({
          name: error.response.errors?.name?.[0],
          document: error.response.errors?.document?.[0],
          email: error.response.errors?.email?.[0],
          status: error.response.errors?.status?.[0],
        });
        setFormError(error.response.message);
      } else {
        setFormError("Não foi possível conectar ao servidor. Tente novamente.");
      }
    } finally {
      setIsSubmitting(false);
    }
  }

  // No cadastro, mostra o banner de sucesso brevemente e então navega de
  // volta para a listagem — erro (422/rede) nunca aciona isso, pois
  // successMessage só é setado no caminho de sucesso.
  useEffect(() => {
    if (mode !== "create" || !successMessage) {
      return;
    }

    const timeout = setTimeout(() => {
      router.push("/clientes");
    }, CREATE_SUCCESS_REDIRECT_DELAY_MS);

    return () => clearTimeout(timeout);
  }, [mode, successMessage, router]);

  return (
    <form onSubmit={handleSubmit} noValidate className="flex flex-col gap-4">
      <div className="flex flex-col gap-1.5">
        <Label htmlFor="name">Nome</Label>
        <Input
          id="name"
          value={name}
          onChange={(event) => setName(event.target.value)}
          aria-invalid={Boolean(fieldErrors.name)}
          disabled={isSubmitting}
        />
        {fieldErrors.name ? <p className="text-sm text-destructive">{fieldErrors.name}</p> : null}
      </div>

      <div className="flex flex-col gap-1.5">
        <Label htmlFor="document">Documento (CPF ou CNPJ)</Label>
        <Input
          id="document"
          inputMode="numeric"
          placeholder="000.000.000-00"
          value={documentNumber}
          onChange={(event) => setDocumentNumber(maskDocument(event.target.value))}
          aria-invalid={Boolean(fieldErrors.document)}
          disabled={isSubmitting}
        />
        {fieldErrors.document ? (
          <p className="text-sm text-destructive">{fieldErrors.document}</p>
        ) : null}
      </div>

      <div className="flex flex-col gap-1.5">
        <Label htmlFor="email">E-mail</Label>
        <Input
          id="email"
          type="email"
          value={email}
          onChange={(event) => setEmail(event.target.value)}
          aria-invalid={Boolean(fieldErrors.email)}
          disabled={isSubmitting}
        />
        {fieldErrors.email ? (
          <p className="text-sm text-destructive">{fieldErrors.email}</p>
        ) : null}
      </div>

      <div className="flex flex-col gap-1.5">
        <Label htmlFor="status">Status</Label>
        <Select
          value={status}
          onValueChange={(value) => setStatus(value as CustomerStatus)}
          disabled={isSubmitting}
        >
          <SelectTrigger id="status" className="w-full sm:w-48">
            <SelectValue />
          </SelectTrigger>
          <SelectContent>
            <SelectItem value="active">Ativo</SelectItem>
            <SelectItem value="inactive">Inativo</SelectItem>
          </SelectContent>
        </Select>
        {fieldErrors.status ? (
          <p className="text-sm text-destructive">{fieldErrors.status}</p>
        ) : null}
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

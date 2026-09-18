import { formatCurrency } from "@/lib/format";

/**
 * Funções puras de máscara/parsing — sem estado, sem DOM, testáveis
 * isoladamente sem montar nenhum componente. Preferido a uma biblioteca
 * (ex: react-input-mask, imask) porque os três casos aqui (CPF/CNPJ,
 * moeda BRL, percentual) são padrões fixos e simples o bastante para não
 * justificar uma nova dependência — ver README para o raciocínio completo.
 */

export function onlyDigits(value: string): string {
  return value.replace(/\D/g, "");
}

/**
 * CPF (11 dígitos, 000.000.000-00) ou CNPJ (14 dígitos, 00.000.000/0000-00),
 * detectado dinamicamente pela quantidade de dígitos já digitados — sem
 * validar dígito verificador, só formatação (fora de escopo).
 *
 * O valor retornado é exatamente o que deve ser enviado à API: os registros
 * já existentes no banco (seeder/factories) persistem `document` COM a
 * pontuação, então o form mantém essa mesma formatação para não introduzir
 * uma segunda convenção conflitante.
 */
export function maskDocument(rawValue: string): string {
  const digits = onlyDigits(rawValue).slice(0, 14);

  return digits.length <= 11 ? formatCpfDigits(digits) : formatCnpjDigits(digits);
}

function formatCpfDigits(digits: string): string {
  let result = digits.slice(0, 3);

  if (digits.length > 3) result += "." + digits.slice(3, 6);
  if (digits.length > 6) result += "." + digits.slice(6, 9);
  if (digits.length > 9) result += "-" + digits.slice(9, 11);

  return result;
}

function formatCnpjDigits(digits: string): string {
  let result = digits.slice(0, 2);

  if (digits.length > 2) result += "." + digits.slice(2, 5);
  if (digits.length > 5) result += "." + digits.slice(5, 8);
  if (digits.length > 8) result += "/" + digits.slice(8, 12);
  if (digits.length > 12) result += "-" + digits.slice(12, 14);

  return result;
}

/**
 * Parsing "cents-first", padrão comum em inputs de dinheiro/percentual BR:
 * os dígitos digitados são sempre lidos da direita para a esquerda como
 * parte decimal (ex: digitar "1234" dá 12,34; digitar mais um dígito "12345"
 * dá 123,45). Compartilhado entre moeda e percentual — a única diferença
 * entre os dois é a formatação de exibição, não o parsing.
 */
export function parseTwoDecimalInput(rawValue: string): number {
  const digits = onlyDigits(rawValue);

  return digits ? Number(digits) / 100 : 0;
}

// Reaproveita formatCurrency (mesma formatação já usada no resto do app,
// ex: tabelas de clientes/cobranças/relatório) em vez de um formato próprio
// só para o input.
export function formatCurrencyInput(value: number): string {
  return formatCurrency(value);
}

const PERCENTAGE_FORMATTER = new Intl.NumberFormat("pt-BR", {
  minimumFractionDigits: 2,
  maximumFractionDigits: 2,
});

export function formatPercentageInput(value: number): string {
  return `${PERCENTAGE_FORMATTER.format(value)}%`;
}

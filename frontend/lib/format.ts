export function formatCurrency(value: number): string {
  return new Intl.NumberFormat("pt-BR", { style: "currency", currency: "BRL" }).format(value);
}

// Para timestamps completos (ISO com hora e timezone, ex: created_at/updated_at).
export function formatDateTime(value: string): string {
  return new Date(value).toLocaleDateString("pt-BR");
}

// Para datas puras "YYYY-MM-DD" sem componente de hora (ex: issue_date,
// due_date, payment_date). new Date(string) interpreta uma string sem hora
// como meia-noite UTC — ao formatar de volta no fuso local, fusos atrás de
// UTC (ex: Brasil) exibem o dia anterior. Construir a partir dos componentes
// numéricos evita essa conversão de fuso.
export function formatDateOnly(value: string): string {
  const [year, month, day] = value.split("-").map(Number);

  return new Date(year, month - 1, day).toLocaleDateString("pt-BR");
}

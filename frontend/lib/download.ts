/**
 * Dispara o "Salvar como" do browser para um Blob já em memória — usado
 * quando o arquivo precisa vir de uma requisição autenticada (fetch com
 * header Authorization) em vez de um link direto, que não teria como levar
 * esse header.
 */
export function downloadBlob(blob: Blob, filename: string): void {
  const url = URL.createObjectURL(blob);

  const link = document.createElement("a");
  link.href = url;
  link.download = filename;
  document.body.appendChild(link);
  link.click();
  document.body.removeChild(link);

  URL.revokeObjectURL(url);
}

"use client";

import { useEffect, useState } from "react";
import { Input } from "@/components/ui/input";
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";

const SEARCH_DEBOUNCE_MS = 400;

interface CustomerFiltersProps {
  status: string;
  search: string;
  onChange: (patch: Record<string, string | null>) => void;
}

export function CustomerFilters({ status, search, onChange }: CustomerFiltersProps) {
  const [searchInput, setSearchInput] = useState(search);
  // Ajuste de estado durante a renderização (em vez de um efeito) para
  // re-sincronizar o campo quando o filtro muda por outra via — ex: voltar
  // pelo histórico do navegador. Ver: https://react.dev/learn/you-might-not-need-an-effect#adjusting-some-state-when-a-prop-changes
  const [prevSearch, setPrevSearch] = useState(search);

  if (search !== prevSearch) {
    setPrevSearch(search);
    setSearchInput(search);
  }

  useEffect(() => {
    const timeout = setTimeout(() => {
      if (searchInput !== search) {
        onChange({ search: searchInput || null, page: null });
      }
    }, SEARCH_DEBOUNCE_MS);

    return () => clearTimeout(timeout);
  }, [searchInput, search, onChange]);

  return (
    <div className="flex flex-col gap-3 sm:flex-row sm:items-center">
      <Input
        placeholder="Buscar por nome, documento ou e-mail"
        value={searchInput}
        onChange={(event) => setSearchInput(event.target.value)}
        className="sm:max-w-xs"
      />

      <Select
        value={status || "all"}
        onValueChange={(value) => onChange({ status: value === "all" ? null : value, page: null })}
      >
        <SelectTrigger className="sm:w-40">
          <SelectValue placeholder="Status" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem value="all">Todos os status</SelectItem>
          <SelectItem value="active">Ativo</SelectItem>
          <SelectItem value="inactive">Inativo</SelectItem>
        </SelectContent>
      </Select>
    </div>
  );
}

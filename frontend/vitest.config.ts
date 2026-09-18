import path from "node:path";
import { defineConfig } from "vitest/config";

// Setup mínimo de propósito: só testamos funções puras (lib/masks.ts), sem
// DOM/componentes — não precisa de jsdom nem @testing-library. Ver README
// para o custo-benefício de ter adicionado isso.
export default defineConfig({
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "."),
    },
  },
  test: {
    environment: "node",
    include: ["**/*.test.ts"],
  },
});

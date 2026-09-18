import { describe, expect, it } from "vitest";
import {
  onlyDigits,
  maskDocument,
  parseTwoDecimalInput,
  formatCurrencyInput,
  formatPercentageInput,
} from "@/lib/masks";

describe("onlyDigits", () => {
  it("strips everything that is not a digit", () => {
    expect(onlyDigits("123.456.789-00")).toBe("12345678900");
    expect(onlyDigits("R$ 1.234,56")).toBe("123456");
    expect(onlyDigits("")).toBe("");
  });
});

describe("maskDocument", () => {
  it("formats progressively as a CPF while <= 11 digits are typed", () => {
    expect(maskDocument("1")).toBe("1");
    expect(maskDocument("123")).toBe("123");
    expect(maskDocument("1234")).toBe("123.4");
    expect(maskDocument("123456")).toBe("123.456");
    expect(maskDocument("1234567")).toBe("123.456.7");
    expect(maskDocument("123456789")).toBe("123.456.789");
    expect(maskDocument("12345678900")).toBe("123.456.789-00");
  });

  it("switches to CNPJ formatting once a 12th digit is typed", () => {
    expect(maskDocument("123456789001")).toBe("12.345.678/9001");
    expect(maskDocument("12345678900123")).toBe("12.345.678/9001-23");
  });

  it("ignores non-digit characters already present in the input", () => {
    expect(maskDocument("123.456.789-00")).toBe("123.456.789-00");
    expect(maskDocument("12.345.678/9001-23")).toBe("12.345.678/9001-23");
  });

  it("caps at 14 digits (max CNPJ length)", () => {
    // Só os 14 primeiros dígitos de "123456789001234567" (...234567 é
    // descartado) são considerados: "12345678900123".
    expect(maskDocument("123456789001234567")).toBe("12.345.678/9001-23");
  });

  it("returns an empty string for empty input", () => {
    expect(maskDocument("")).toBe("");
  });
});

describe("parseTwoDecimalInput", () => {
  it("reads typed digits as cents (cents-first masking)", () => {
    expect(parseTwoDecimalInput("1")).toBeCloseTo(0.01);
    expect(parseTwoDecimalInput("12")).toBeCloseTo(0.12);
    expect(parseTwoDecimalInput("1234")).toBeCloseTo(12.34);
    expect(parseTwoDecimalInput("123456")).toBeCloseTo(1234.56);
  });

  it("ignores non-digit characters (currency symbol, thousands separator)", () => {
    expect(parseTwoDecimalInput("R$ 1.234,56")).toBeCloseTo(1234.56);
    expect(parseTwoDecimalInput("2,50%")).toBeCloseTo(2.5);
  });

  it("returns 0 for empty or non-numeric input", () => {
    expect(parseTwoDecimalInput("")).toBe(0);
    expect(parseTwoDecimalInput("abc")).toBe(0);
  });

  it("never produces a negative number regardless of input", () => {
    expect(parseTwoDecimalInput("-1234")).toBeCloseTo(12.34);
  });
});

describe("formatCurrencyInput", () => {
  it("formats using the same BRL currency convention as the rest of the app", () => {
    expect(formatCurrencyInput(1234.56)).toBe("R$ 1.234,56");
    expect(formatCurrencyInput(0)).toBe("R$ 0,00");
  });
});

describe("formatPercentageInput", () => {
  it("formats with a comma decimal separator and a visual % suffix", () => {
    expect(formatPercentageInput(2.5)).toBe("2,50%");
    expect(formatPercentageInput(0)).toBe("0,00%");
    expect(formatPercentageInput(100)).toBe("100,00%");
  });
});

describe("mask -> parse round trip", () => {
  it("parsing a formatted currency string back recovers the original value", () => {
    const value = 1987.65;
    const formatted = formatCurrencyInput(value);
    expect(parseTwoDecimalInput(formatted)).toBeCloseTo(value);
  });

  it("parsing a formatted percentage string back recovers the original value", () => {
    const value = 4.25;
    const formatted = formatPercentageInput(value);
    expect(parseTwoDecimalInput(formatted)).toBeCloseTo(value);
  });
});

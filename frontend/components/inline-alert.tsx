interface InlineAlertProps {
  variant: "success" | "error";
  message: string;
}

// Banner inline por ora — toast é melhoria futura registrada no CLAUDE.md.
export function InlineAlert({ variant, message }: InlineAlertProps) {
  const isError = variant === "error";

  return (
    <p
      role={isError ? "alert" : "status"}
      className={
        isError
          ? "rounded-md bg-destructive/10 px-3 py-2 text-sm text-destructive"
          : "rounded-md bg-primary/10 px-3 py-2 text-sm text-primary"
      }
    >
      {message}
    </p>
  );
}

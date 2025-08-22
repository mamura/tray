import { format } from "date-fns";
import { ptBR } from "date-fns-locale";

export const toBR = (iso: string | Date) =>
  format(typeof iso === 'string' ? new Date(iso) : iso, 'dd/MM/yyyy', { locale:ptBR });

export const money = (v: number) =>
  new Intl.NumberFormat('pt-BR', {style: 'currency', currency: 'BRL'}).format(v);

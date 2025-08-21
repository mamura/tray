import type { AxiosError } from "axios";
import axios, { isAxiosError } from "axios";


export function isAxioserror<T = any>(e:unknown): e is AxiosError<T> {
  return axios.isAxiosError(e);
}

export function extractErrorMessage(e: unknown): string {
  if (isAxiosError(e)) {
    return (e.response?.data as any)?.message ?? e.message;
  }

  if (e instanceof Error) {
    return e.message;
  }

  try {
    return  JSON.stringify(e);
  } catch {
    return String(e);
  }
}

export function extractStatus(e: unknown): number | undefined {
  return isAxiosError(e) ? e.response?.status : undefined;
}

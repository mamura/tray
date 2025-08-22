import type { AxiosError, AxiosInstance } from "axios";
import type { ApiError, ApiValidationErrors } from "../types/http";

function isRecordOfStringArray(value: unknown): value is ApiValidationErrors {
  if (typeof value !== 'object' || value === null)
  {
    return false;
  }

  const obj = value as Record<string, unknown>

  return Object.values(obj).every(
    (v) => Array.isArray(v) && v.every((s) => typeof s === 'string'),
  );
}

function getProp<T>(obj: unknown, key: string): T | undefined {
  if (typeof obj !== 'object' || obj === null) {
    return undefined;
  }

  const rec = obj as Record<string, unknown>

  if (!(key in rec)) {
    return undefined;
  }

  return rec[key] as T
}

export function installInterceptors(http: AxiosInstance) {
  http.interceptors.response.use(
    (r) => r,
    (error: AxiosError<unknown>) => {
      const err: ApiError = new Error(error.message);
      const status = error.response?.status;

      if (typeof status === 'number') {
        err.status = status;
      }

      if (error.response?.status === 422) {
        const errorsUnknown = getProp<unknown>(error.response.data, 'errors');

        if (isRecordOfStringArray(errorsUnknown)) {
          err.validation = errorsUnknown;
        }

      }

      return Promise.reject(err);
    }
  );
}

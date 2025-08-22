export type Paginated<T> = {
  data: T[];
  meta?: {
    current_page: number;
    per_page: number;
    total: number;
    last_pag?: number;
  };
}

export type ApiValidationErrors = Record<string, string[]>

export interface ApiError extends Error {
  status?: number;
  validation?: ApiValidationErrors
}



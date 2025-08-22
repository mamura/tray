import { useNotifyStore } from "@/stores/notify";

export function useNotify() {
  const s = useNotifyStore();

  return {
    success: (m: string, t?: number) => s.success(m, t),
    error:   (m: string, t?: number) => s.error(m, t),
    info:    (m: string, t?: number) => s.info(m, t),
  }
}

import { useAuthStore } from '@/features/auth/store/useAuthStore';
import { router } from '@app/router';
import axios, { AxiosError, type AxiosRequestConfig, type AxiosRequestHeaders, type InternalAxiosRequestConfig } from 'axios';

type RetryableConfig =
  | (InternalAxiosRequestConfig & {_retry?: boolean})
  | (AxiosRequestConfig & {_retry?: boolean})

export const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL ?? 'http://localhost:8000',
  withCredentials: true,
  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',
});

api.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

let handlingAuthError = false;

function getCookie(name: string) {
  const m = document.cookie.match(new RegExp('(?:^|; )' + name.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, '\\$&') + '=([^;]*)'));
  return m ? decodeURIComponent(m[1]) : null
}

api.interceptors.request.use((config) => {
  const headers = (config.headers ??= {} as AxiosRequestHeaders)
  if (!('X-XSRF-TOKEN' in headers)) {
    const xsrf = getCookie('XSRF-TOKEN')
    if (xsrf) headers['X-XSRF-TOKEN'] = xsrf
  }
  return config
})

api.interceptors.response.use(
  (r) => r,
  async (error: AxiosError) => {
    const status = error.response?.status;
    const config = (error.config ?? {}) as RetryableConfig;

    if (status === 419 && !config._retry) {
      config._retry = true;
      try {
        await api.get('sanctum/csrf-cookie');
        return api(config);
      } catch {}
    }

    if (status === 401 && !handlingAuthError) {
      handlingAuthError = true;

      try {
        const auth = useAuthStore();
        await auth.logout().catch(() => {});
        const current = router.currentRoute.value;

        if (current.path !== '/login') {
          await router.replace({ path: '/login', query: { redirect: current.fullPath } });
        }
      } finally {
        setTimeout(() => (handlingAuthError = false), 500);
      }
    }

    return Promise.reject(error);
  }
);

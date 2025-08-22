import axios from 'axios';
import { env } from '@shared/config/env';
import { installInterceptors } from './interceptors';

export const api = axios.create({
  baseURL: env.API_URL,
  headers: { 'COntent-Type': 'application/json', Accept: 'application/json' },
  withCredentials: true,
});

installInterceptors(api);

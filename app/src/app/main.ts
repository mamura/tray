import '@shared/styles/global.css'
import '@fortawesome/fontawesome-free/css/all.min.css';
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue';
import { router } from './router';

const app = createApp(App);
const pinia = createPinia();

app.use(pinia)
app.use(router)

app.config.errorHandler = (err, instance, info) => {
  //TODO: logar para Sentry/LogROcket, etc.
  console.error('[GlobalError]', err, info)
}

app.mount('#app')

if (import.meta.env.DEV) {
  // carregado dinâmico para evitar ciclos
  import('@/stores/notify').then(({ useNotifyStore }) => {
    // @ts-expect-error: expõe no window só para debug
    window.notify = useNotifyStore(pinia)
    // no console: notify.success('funcionou!')
  })
}

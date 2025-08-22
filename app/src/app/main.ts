import '@shared/styles/global.css'
import '@fortawesome/fontawesome-free/css/all.min.css';
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import App from './App.vue';
import { router } from './router';

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.config.errorHandler = (err, instance, info) => {
  //TODO: logar para Sentry/LogROcket, etc.
  console.error('[GlobalError]', err, info)
}

app.mount('#app')

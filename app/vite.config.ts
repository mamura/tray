import { fileURLToPath, URL } from 'node:url'

import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import vueDevTools from 'vite-plugin-vue-devtools'
import tailwindcss from '@tailwindcss/vite'

export default defineConfig(({ mode }) => {
  const isProd = mode === 'production';

  return {
    plugins: [
      vue(),
      !isProd && vueDevTools(),
      tailwindcss()
    ].filter(Boolean),

    resolve: {
      alias: {
        '@': fileURLToPath(new URL('./src', import.meta.url)),
        '@app': fileURLToPath(new URL('./src/app', import.meta.url)),
        '@shared': fileURLToPath(new URL('./src/shared', import.meta.url)),
        '@features': fileURLToPath(new URL('./src/features', import.meta.url)),
        '@entities': fileURLToPath(new URL('./src/entities', import.meta.url)),
        '@pages': fileURLToPath(new URL('./src/pages', import.meta.url)),
        '@widgets': fileURLToPath(new URL('./src/widgets', import.meta.url)),
      },
    },

    css: { devSourcemap: true },

    server: {
      port: 5173,
      proxy: { '/api': { target: 'http://localhost', changeOrigin: true } }
    },

    build: {
      sourcemap: false,
      chunkSizeWarningLimit: 1024
    }
  }
});

/*
// https://vite.dev/config/
export default defineConfig({
  plugins: [
    vue(),
    vueDevTools(),
    tailwindcss()
  ],
  resolve: {
    alias: {
      '@': fileURLToPath(new URL('./src', import.meta.url)),
      '@app': '/src/app',
      '@shared': '/src/shared',
      '@features': '/src/features',
      '@entities': '/src/entities',
      '@pages': '/src/pages',
      '@widgets': '/src/widgets'
    },
  },
})
*/

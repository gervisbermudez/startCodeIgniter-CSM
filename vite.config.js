import { defineConfig } from 'vite';
import { resolve } from 'path';

// Configuración simplificada: SOLO compila SCSS
// El JavaScript se maneja directamente sin procesar (legacy code compatible)
export default defineConfig({
  publicDir: false,
  base: '/public/', // Base URL para assets
  build: {
    outDir: 'public',
    emptyOutDir: false,
    assetsInlineLimit: 0, // No inline assets
    rollupOptions: {
      input: {
        // Solo SCSS - todos los archivos principales
        'css/admin/materialize.min': resolve(__dirname, 'resources/scss/admin/materialize.scss'),
        'css/admin/dashboard.min': resolve(__dirname, 'resources/scss/admin/dashboard.scss'),
        'css/admin/file_explorer.min': resolve(__dirname, 'resources/scss/admin/file_explorer.scss'),
        'css/admin/form.min': resolve(__dirname, 'resources/scss/admin/form.scss'),
        'css/admin/header.min': resolve(__dirname, 'resources/scss/admin/header.scss'),
        'css/admin/login.min': resolve(__dirname, 'resources/scss/admin/login.scss'),
        'css/admin/page-new.min': resolve(__dirname, 'resources/scss/admin/page-new.scss'),
        'css/admin/skeleton-loader.min': resolve(__dirname, 'resources/scss/admin/skeleton-loader.scss'),
        'css/admin/start.min': resolve(__dirname, 'resources/scss/admin/start.scss'),
        'css/admin/userprofile.min': resolve(__dirname, 'resources/scss/admin/userprofile.scss'),
      },
      output: {
        assetFileNames: (assetInfo) => {
          if (assetInfo.name && assetInfo.name.endsWith('.css')) {
            return '[name].css';
          }
          // Fuentes a fonts/, resto a assets/
          if (assetInfo.name && (assetInfo.name.endsWith('.woff') || 
              assetInfo.name.endsWith('.woff2') || 
              assetInfo.name.endsWith('.ttf') || 
              assetInfo.name.endsWith('.eot'))) {
            return 'fonts/[name].[ext]';
          }
          return 'assets/[name].[ext]';
        },
      },
      external: ['/public/fonts/*']
    },
  },
  css: {
    preprocessorOptions: {
      scss: {
        api: 'modern'
      },
    },
  },
});

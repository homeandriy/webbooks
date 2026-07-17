import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';

export default defineConfig({
  base: './',
  resolve: {
    alias: {
      jquery: fileURLToPath( new URL( './src/runtime/wordpress-jquery.cjs', import.meta.url ) ),
    },
  },
  build: {
    manifest: true,
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: fileURLToPath( new URL( './src/main.js', import.meta.url ) ),
      },
      output: {
        entryFileNames: 'assets/webbooks.[hash].js',
        chunkFileNames: 'assets/webbooks.[hash].js',
        assetFileNames: 'assets/webbooks.[hash][extname]',
      },
    },
  },
});

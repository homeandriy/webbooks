import { defineConfig } from 'vite';
import { fileURLToPath } from 'node:url';

export default defineConfig({
  base: './',
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

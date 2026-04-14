import { defineConfig } from 'vite';
import path from 'node:path';

export default defineConfig({
  base: './',
  build: {
    manifest: true,
    outDir: 'dist',
    emptyOutDir: true,
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'src/main.js'),
      },
      output: {
        entryFileNames: 'assets/webbooks.[hash].js',
        chunkFileNames: 'assets/webbooks.[hash].js',
        assetFileNames: 'assets/webbooks.[hash][extname]',
      },
    },
  },
});

import { resolve } from 'path'

export default {
  root: resolve(__dirname, 'src'),
  build: {
    outDir: '../dist',
    rollupOptions: {
      input: {
        index: resolve(__dirname, 'src/index.html'),
        backend: resolve(__dirname, 'src/js/backend.js'),
      },
    },
  },
  server: {
    port: 8080
  }
}
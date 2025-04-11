import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import fs from 'fs';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.scss',
                'resources/js/app.js',
                'resources/js/paneles/rrhh-valentina.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        host: 'localhost',
        port: 5173,
        strictPort: true,
        hmr: {
            host: 'localhost',
            protocol: 'ws'
        },
        cors: {
            origin: true,
            credentials: true
        },
        // Descomenta para HTTPS en desarrollo (necesitas certificados)
        /* https: {
            key: fs.readFileSync('path/to/localhost-key.pem'),
            cert: fs.readFileSync('path/to/localhost.pem')
        } */
    },
    build: {
        manifest: true,
        rollupOptions: {
            external: ['jquery', 'bootstrap', 'admin-lte'],
            output: {
                assetFileNames: 'assets/[name]-[hash][extname]',
                entryFileNames: 'assets/[name]-[hash].js'
            }
        }
    },
    optimizeDeps: {
        include: ['jquery', 'bootstrap', 'admin-lte']
    }
});
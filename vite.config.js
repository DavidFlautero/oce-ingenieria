import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

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
            host: 'localhost'
        },
        cors: {
            origin: 'http://localhost:8000', // URL de tu servidor Laravel
            credentials: true
        }
    }
});
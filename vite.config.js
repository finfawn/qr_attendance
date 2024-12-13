import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
            publicDirectory: 'public',
        }),
    ],
    server: {
        hmr: {
            host: 'localhost'
        },
    },
    build: {
        manifest: true,
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    qr: ['qr-scanner']
                }
            }
        }
    },
    optimizeDeps: {
        include: ['qr-scanner']
    }
});

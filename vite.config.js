import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/index.css',
                'resources/js/index.js',
                'resources/css/admin.css',
                'resources/css/portal.css',
                'resources/js/admin.js',
            ],
            refresh: false,
        }),
    ],
});

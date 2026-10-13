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
                'resources/css/gallery-page.css',
                'resources/js/gallery-page.js',
                'resources/css/public-transition.css',
                'resources/js/public-transition.js',
                'resources/js/admin.js',
            ],
            refresh: false,
        }),
    ],
});

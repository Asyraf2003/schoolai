import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/js/pages/welcome.js',
                'resources/js/pages/welcome-hero.js',
                // About section is temporarily disabled.
                // 'resources/js/pages/welcome-about-stats.js',
                'resources/js/pages/welcome-testimonial-story.js',
                'resources/js/pages/welcome-testimonial-extra-nodes.js',
                'resources/js/pages/welcome-scroll-reveal.js',
                'resources/js/pages/welcome-editorial-headings.js',
                'resources/js/pages/ppdb-journey.js',
                'resources/css/pages/welcome.css',
                'resources/css/pages/welcome-hero.css',
                // 'resources/css/pages/welcome-about-stats.css',
                'resources/css/pages/welcome-testimonial-layout.css',
                'resources/css/pages/welcome-mega-menu.css',
                'resources/css/pages/welcome-hero-motion.css',
                'resources/css/pages/welcome-hero-visual.css',
                'resources/css/pages/welcome-scroll-reveal.css',
                'resources/css/pages/welcome-editorial-headings.css',
                'resources/css/pages/welcome-editorial-description-desktop.css',
                // 'resources/css/pages/welcome-about-editorial-override.css',
                'resources/css/pages/ppdb-journey.css',
                'resources/css/pages/article-canvas.css',
                'resources/css/pages/article-reader.css',
                'resources/css/text-system.css',
                'resources/css/arabic-typography.css',
                'resources/css/public-latin-inter.css',
                'resources/js/pages/article-canvas.js',
                'resources/js/pages/article-canvas-arabic.js',
                'resources/js/pages/article-canvas-context-ui.js',
                'resources/css/pages/account-locked.css',
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
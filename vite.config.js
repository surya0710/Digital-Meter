import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/energy-meter.css',
                'resources/js/app.js',
                'resources/js/device/panel/index.js',
                'resources/js/device/energy-meter/index.js',
            ],
            refresh: true,
        }),
    ],
});

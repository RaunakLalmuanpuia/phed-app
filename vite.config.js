import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import {quasar,transformAssetUrls} from "@quasar/vite-plugin";
import tailwindcss from '@tailwindcss/vite'
import path from 'path';
export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        quasar({
            sassVariables: path.resolve(__dirname, 'resources/css/variables.sass'),
        }),
        tailwindcss(),
    ],
});

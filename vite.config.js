import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        https: true, // Asegúrate de que HTTPS esté habilitado si es necesario
        //port: 5173, // Puedes especificar el puerto que prefieras
        host: '0.0.0.0', // Esto permite que el servidor escuche en todas las interfaces de red disponibles
    },
    resolve: {
        alias: {
            '@inertiajs/vue3': '/node_modules/@inertiajs/vue3/dist/index.mjs',
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/js/app.js',
                'resources/css/app.css',  // Incluye el archivo CSS aquí
            ],
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
    ],
});

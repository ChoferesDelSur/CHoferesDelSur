import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        //port: 5173, // Puedes especificar el puerto que prefieras
        host: '192.168.0.13', // Esto permite que el servidor escuche en todas las interfaces de red disponibles
    },
    resolve: {
        alias: {
            '@inertiajs/vue3': '/node_modules/@inertiajs/vue3/dist/index.mjs',
        },
    },
    plugins: [
        laravel({
            input: 'resources/js/app.js',
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
    /* build: {
        outDir: 'public/build', // Configura la carpeta de salida
        manifest: true,
    }, */
});

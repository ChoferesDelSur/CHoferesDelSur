import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        //port: 5173, // Puedes especificar el puerto que prefieras
        host: '192.168.0.30', // Esto permite que el servidor escuche en todas las interfaces de red disponibles
        // cuarto = '192.168.1.94'
        //trabajo = 192.168.0.55
    },
    resolve: {
        alias: {
            '@inertiajs/vue3': '/node_modules/@inertiajs/vue3/dist/index.mjs',
            'pdfmake/build/pdfmake': 'pdfmake/build/pdfmake.js',
            'pdfmake/build/vfs_fonts': 'pdfmake/build/vfs_fonts.js',
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
});

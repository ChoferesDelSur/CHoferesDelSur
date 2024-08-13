import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        //port: 5173, // Puedes especificar el puerto que prefieras
        host: '192.168.0.14', // Esto permite que el servidor escuche en todas las interfaces de red disponibles
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
    optimizeDeps: {
        include: [
            'pdfmake/build/pdfmake',  // Incluye pdfmake para optimización de dependencias
            'pdfmake/build/vfs_fonts' // Incluye vfs_fonts para optimización de dependencias
        ],
    },
    assetsInclude: [
        'pdfmake/build/pdfmake',   // Incluye pdfmake y sus archivos necesarios
        'pdfmake/build/vfs_fonts', // Incluye vfs_fonts y sus archivos necesarios
        // Puedes agregar más rutas de archivos de fuentes u otros recursos aquí si es necesario
    ],
});

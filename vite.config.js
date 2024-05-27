import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        //port: 5173, // Puedes especificar el puerto que prefieras
        host: '192.168.0.51', // Esto permite que el servidor escuche en todas las interfaces de red disponibles
        // cuarto = '192.168.1.94'
        //trabajo = 192.168.0.55
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

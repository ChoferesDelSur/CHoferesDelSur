import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        //port: 5173, // Puedes especificar el puerto que prefieras
        host: '192.168.0.117', // Esto permite que el servidor escuche en todas las interfaces de red disponibles
        // cuarto = '192.168.1.94'
        //trabajo = 192.168.0.117
      }, 
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});

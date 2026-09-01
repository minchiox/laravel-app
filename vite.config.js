import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    // Necessario per l'HMR dal container `node`: senza host 0.0.0.0 il dev
    // server ascolta solo sul loopback interno e il browser non lo raggiunge.
    // La porta arriva da .env (VITE_PORT) perche' 5173 e' spesso occupata.
    server: {
        host: '0.0.0.0',
        port: Number(process.env.VITE_PORT) || 5273,
        strictPort: true,
        hmr: {
            host: 'localhost',
            clientPort: Number(process.env.VITE_PORT) || 5273,
        },
        watch: {
            // i bind mount non propagano gli eventi inotify
            usePolling: true,
        },
    },
});

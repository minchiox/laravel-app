import react from '@vitejs/plugin-react';
import tailwindcss from '@tailwindcss/vite';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                // Blade + Bootstrap: lo Step F2 ha convertito tutte le pagine a
                // Inertia+React, ma resta in vita per i due template di stampa
                // PDF (resources/views/exam/printResult*.blade.php), gli unici
                // ancora renderizzati via dompdf invece che dalla SPA.
                'resources/sass/app.scss',
                'resources/js/app.js',
                // Inertia + React (Step F1-F2, tutta l'app tranne la stampa).
                'resources/css/app.css',
                'resources/js/app.tsx',
            ],
            refresh: true,
        }),
        react(),
        tailwindcss(),
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

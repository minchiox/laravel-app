import { createInertiaApp } from '@inertiajs/react';
import { createRoot } from 'react-dom/client';

const appName = import.meta.env.VITE_APP_NAME || 'MEXAM';

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) => {
        const pages = import.meta.glob<{ default: React.ComponentType }>('./pages/**/*.tsx', { eager: true });
        const page = pages[`./pages/${name}.tsx`];

        if (!page) {
            throw new Error(`Pagina Inertia non trovata: ./pages/${name}.tsx`);
        }

        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
});

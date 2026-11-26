import { createRoot } from 'react-dom/client';
import { createInertiaApp } from '@inertiajs/react';

const pages = import.meta.glob('./Pages/**/*.tsx', { eager: true });

createInertiaApp({
    title: (title) => (title ? `${title} — MaintainNow` : 'MaintainNow'),
    resolve: (name) => {
        const page = pages[`./Pages/${name}.tsx`];
        if (!page) {
            throw new Error(`Inertia page not found: ${name}`);
        }

        return page;
    },
    setup({ el, App, props }) {
        createRoot(el).render(<App {...props} />);
    },
    progress: {
        color: '#1d5fbf',
        showSpinner: false,
    },
});

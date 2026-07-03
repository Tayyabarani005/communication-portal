import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                primary: {
                    50: 'var(--color-primary-50)',
                    100: 'var(--color-primary-100)',
                    200: 'var(--color-primary-200)',
                    300: 'var(--color-primary-300)',
                    400: 'var(--color-primary-400)',
                    500: 'var(--color-primary-500)',
                    600: 'var(--color-primary-600)',
                    700: 'var(--color-primary-700)',
                    800: 'var(--color-primary-800)',
                    900: 'var(--color-primary-900)',
                    950: 'var(--color-primary-950)',
                },
                accent: {
                    50: 'var(--color-accent-50)',
                    100: 'var(--color-accent-100)',
                    200: 'var(--color-accent-200)',
                    300: 'var(--color-accent-300)',
                    400: 'var(--color-accent-400)',
                    500: 'var(--color-accent-500)',
                    600: 'var(--color-accent-600)',
                    700: 'var(--color-accent-700)',
                    800: 'var(--color-accent-800)',
                    900: 'var(--color-accent-900)',
                    950: 'var(--color-accent-950)',
                },
                sidebar: {
                    bg: 'var(--color-sidebar-bg)',
                    text: 'var(--color-sidebar-text)',
                    'text-active': 'var(--color-sidebar-text-active)',
                    'hover-bg': 'var(--color-sidebar-hover-bg)',
                    'active-bg': 'var(--color-sidebar-active-bg)',
                },
                pin: {
                    bg: 'var(--color-pin-bg)',
                    border: 'var(--color-pin-border)',
                    text: 'var(--color-pin-text)',
                },
                bgMain: 'var(--color-bg-main)',
                cardBg: 'var(--color-card-bg)',
                borderColor: 'var(--color-border)',
            },
        },
    },

    plugins: [forms],
};

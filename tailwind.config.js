import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
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
            // Warna brand "emerald" dipetakan ke CSS variable agar tiap owner bisa
            // mengganti warna dasar dashboard-nya. Default (:root di app.css) = emerald asli,
            // jadi tanpa override tampilannya identik. Override di-inject per-owner di layout.
            colors: {
                emerald: {
                    50: 'rgb(var(--c-em-50) / <alpha-value>)',
                    100: 'rgb(var(--c-em-100) / <alpha-value>)',
                    200: 'rgb(var(--c-em-200) / <alpha-value>)',
                    300: 'rgb(var(--c-em-300) / <alpha-value>)',
                    400: 'rgb(var(--c-em-400) / <alpha-value>)',
                    500: 'rgb(var(--c-em-500) / <alpha-value>)',
                    600: 'rgb(var(--c-em-600) / <alpha-value>)',
                    700: 'rgb(var(--c-em-700) / <alpha-value>)',
                    800: 'rgb(var(--c-em-800) / <alpha-value>)',
                    900: 'rgb(var(--c-em-900) / <alpha-value>)',
                    950: 'rgb(var(--c-em-950) / <alpha-value>)',
                },
            },
        },
    },

    plugins: [forms],
};

import forms from '@tailwindcss/forms';
import rtl from 'tailwindcss-rtl';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            colors: {
                primary: '#3B6EF8',
                'primary-light': '#EEF2FF',
                'primary-dark': '#2A52C9',
                bg: '#F8F9FB',
                surface: '#FFFFFF',
                border: '#E4E7EC',
                'text-primary': '#111827',
                'text-secondary': '#6B7280',
                'text-disabled': '#B0B7C3',
                success: '#16A34A',
                'success-bg': '#F0FDF4',
                warning: '#D97706',
                'warning-bg': '#FFFBEB',
                danger: '#DC2626',
                'danger-bg': '#FEF2F2',
                'danger-subtle': '#FCA5A5',
            },
            fontFamily: {
                sans: ['"IBM Plex Sans Arabic"', 'sans-serif'],
            },
        },
    },
    plugins: [forms, rtl],
};

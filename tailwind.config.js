import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';
const colors = require('tailwindcss/colors');

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
                sans: ['Figtree', 'Inter', 'sans-serif'],
                dashboard: ['Manrope', 'sans-serif'],
                heading: ['Syne', 'sans-serif'],
            },
            colors: {
                indigo: colors.orange,
                brand: {
                    DEFAULT: '#E8470A',
                    50: '#FDF3EE',
                    100: '#FAE4D9',
                    200: '#F3C4B0',
                    300: '#ECA486',
                    400: '#E6845D',
                    500: '#E8470A',
                    600: '#C43606',
                    700: '#942603',
                    800: '#681801',
                    900: '#480E00',
                },
                accent: {
                    green: '#10B981',
                    blue: '#3B82F6',
                    yellow: '#F59E0B'
                }
            }
        },
    },

    plugins: [forms, typography],
};

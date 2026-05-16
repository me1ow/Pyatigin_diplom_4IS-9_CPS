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
                sans: ['Geologica', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                indigo: {
                    50: '#f0fbe4',
                    100: '#ddf5c2',
                    200: '#B8E89A',
                    300: '#a0df76',
                    400: '#8dd45a',
                    500: '#75C040',
                    600: '#75C040',
                    700: '#62A838',
                    800: '#4e8c2a',
                    900: '#3a701e',
                },
            },
        },
    },

    plugins: [forms],
};

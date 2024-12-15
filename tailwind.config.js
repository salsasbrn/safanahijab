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
            height: {
                '1/2screen': '50vh', // Custom class untuk h-1/2screen
            },

            colors: {
                brown: {
                  100: '#F5F5DC',
                  200: '#D2B48C',
                  300: '#A0522D',
                  400: '#786C3B',
                  500: '#654321',
                  600: '#452B1F',
                  700: '#2F1A0F',
                  800: '#1A0F0F',
                  900: '#0F0F0F',
                },
              },
        },

        
    },

    plugins: [
        require('daisyui'),
    ],
};

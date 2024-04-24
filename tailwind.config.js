/** @type {import('tailwindcss').Config} */
const plugin = require('tailwindcss/plugin');

module.exports = {
    content: [
        "./public/**/*.{html, js}",
        "./*.{html, js}"
    ],
    theme: {
        textShadow: {
            sm: '1px 1px 2px var(--tw-shadow-color)',
            DEFAULT: '2px 2px 4px var(--tw-shadow-color)',
            lg: '4px 4px 8px var(--tw-shadow-color)',
            xl: '4px 4px 16px var(--tw-shadow-color)',
        },
        extend: {
            colors: {
                primary: '#c40094ff',
                primaryHover: '#8e006bff',
                secondary: '#4f013bff',
                tertiary: 'lightcyan',
                background: '#09001dff',
                text: '#ffffffff',
                green: '#00a63dff',
                red: '#a60045ff',
                disable: 'rgba(0, 0, 0, 0.3 )'
            },
            fontFamily: {
                poppins: ['Poppins', 'sans-serif'],
            },
            backgroundImage: {
                'home-img': "url('../res/img/bg-home-003.gif')",
            },
        },
    },
    plugins: [
        plugin(function({
            matchUtilities,
            theme
        }) {
            matchUtilities({
                'text-shadow': (value) => ({
                    textShadow: value,
                }),
            }, {
                values: theme('textShadow')
            })
        })
    ],
}
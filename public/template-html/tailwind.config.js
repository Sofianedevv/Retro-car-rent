/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./**/*.{html,js,twig}"],
    theme: {
        opacity: {
            '0': '0',
            '10': '0.1',
            '20': '0.2',
            '30': '0.3',
            '40': '0.4',
            '50': '0.5',
            '60': '0.6',
            '70': '0.7',
            '80': '0.8',
            '90': '0.9',
            '100': '1',
        },
        container: {
            center: true,
            padding: '2rem',
        },
        fontFamily: {
            retro: ['Playfair Display', 'serif'],
            'modern': ['Lato', 'sans-serif'],
            sixtyfour: ['Sixtyfour', 'sans-serif'],
        },
        colors: {
            transparent: 'transparent',
            current: 'currentColor',
            white: '#f9f6f0', // Blanc doux rétro
            black: '#2c2c2c', // Noir élégant
            primary: '#d4af37', // Doré rétro
            secondary: '#5a6e5c', // Vert olive doux
            accent: '#e5c4a1', // Beige chaud pour les contrastes
            gray: {
                lighter: '#f4f1eb', // Gris clair vintage
                light: '#a3a3a3',   // Gris neutre
                dark: '#3e3e3e',    // Gris anthracite
                txt: '#494949',     // Gris textuel
                line: '#d1d1d1',    // Gris pour les séparateurs
            },
        },
        extend: {
            spacing: {
                '18': '4.5rem',
                '22': '5.5rem',
            },
            borderRadius: {
                '4xl': '2.5rem',
            },
            boxShadow: {
                vintage: '0 4px 6px rgba(0, 0, 0, 0.2)', // Ombrage léger
            },
        },
    },
    plugins: [],
}
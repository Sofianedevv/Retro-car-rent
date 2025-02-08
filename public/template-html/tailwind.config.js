/** @type {import('tailwindcss').Config} */
module.exports = {
    content: ["./**/*.{html,js,twig}" ], // inclut tous les fichiers twig et autres fichiers HTML/JS],
    theme: {
        container: {
            center: true,
            padding: '1rem',
        },
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
        },
        fontFamily: {
            'playfair': ['Playfair Display', 'serif'],
        },
        extend: {
            colors: {
                'primary': '#e0d8b0'
            },
        },
    },
    plugins: [],
}

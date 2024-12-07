/** @type {import('tailwindcss').Config} */
module.exports = {
  content: ["./**/*.{html,js,twig}"],
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
     manrope: ['Mangrove', 'sans-serif'],
  },
  colors: {
      transparent: 'transparent',
      current: 'currentColor',
      white: '#fdfcf3', // Blanc cassé pour un effet vintage
      black: '#3b302f', // Noir doux pour les contrastes
      primary: '#d97706', // Orange rétro
      secondary: '#6b705c', // Vert olive doux
      accent: '#cb997e', // Beige rosé pour des touches subtiles
      gray: {
          lighter: '#ede0d4', // Beige clair
          light: '#a5a58d', // Gris olive
          dark: 'rgba(107,112,92,0.83)', // Vert gris
          txt: '#4f4a41', // Brun textuel doux
          line: '#d6ccc2', // Beige clair pour les séparateurs
      },
  },
    extend: {},
  },
  plugins: [],
}

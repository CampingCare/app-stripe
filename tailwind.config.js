/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
        colors: {
            'stripe': {
                100: '#9AB1FF',
                200: '#7D98FF',
                300: '#627FFF',
                400: '#4A63FF',
                500: '#635AFF',
                600: '#4B4DFF',
                700: '#4043FF',
                800: '#363AFF',
                900: '#1A1DFF',
            }
        }
    },
  },
  plugins: [],
}

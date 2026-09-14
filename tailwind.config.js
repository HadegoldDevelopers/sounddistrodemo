const defaultTheme = require('tailwindcss/defaultTheme');

module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './resources/**/*.vue',
  ],
  theme: {
    extend: {
      colors: {
        // Add your logo colors here with meaningful names
        primary: '#0B194E',    // Navy blue from your logo
        secondary: '#D4AF37',  // Gold
        accent: '#000000',     // Black
        background: '#ffffff', // White
      },
      fontFamily: {
        sans: ['Inter', ...defaultTheme.fontFamily.sans],
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'), 
  ],
};

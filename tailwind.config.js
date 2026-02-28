/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ['Tajawal', 'sans-serif'],
      },
      colors: {
        dark: {
          900: '#1a1d21', // Main background
          800: '#212529', // Card background
          700: '#343a40', // Borders/Separators
        },
        primary: {
          500: '#0d6efd', // Blue button
          600: '#0b5ed7',
        },
        success: {
          500: '#198754', // Green button
        },
        warning: {
          500: '#ffc107', // Yellow button
        },
        danger: {
          500: '#dc3545', // Red button
        }
      }
    },
  },
  plugins: [],
}

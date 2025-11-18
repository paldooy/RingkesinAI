/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'ringkesin-blue': '#2C74B3',
        'ringkesin-blue-light': '#A7C7E7',
        'ringkesin-bg': '#F9FAFB',
        'ringkesin-gray': '#E5E7EB',
        'ringkesin-text': '#1E293B',
      },
    },
  },
  plugins: [],
}

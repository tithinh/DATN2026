/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{vue,js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: '#3b82f6', // Màu chính (blue-500)
        secondary: '#10b981', // green-500
      },
    },
  },
  plugins: [],
}
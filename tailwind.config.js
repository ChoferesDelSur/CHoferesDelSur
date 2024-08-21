/** @type {import('tailwindcss').Config} */
export default {
  content: [
    //"./resources/**/*.blade.php",
    //"./resources/**/*.js",
    //"./resources/**/*.vue",
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
  ],
  theme: {
    extend: {},
  },
  plugins: [],
}


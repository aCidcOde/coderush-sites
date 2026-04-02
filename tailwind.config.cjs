module.exports = {
  content: [
    "./sistemavendadireta/index.php",
    "./sistemavendadireta/components/**/*.php",
    "./sistemavendadireta/blog/**/*.php",
    "./sistemavendadireta/inteligencia-artificial/**/*.php",
    "./sistemavendadireta/wordpress/**/*.php",
    "./sistemavendadireta/2023/**/*.php",
    "./sistemavendadireta/2026/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        brand: "#004AAD",
        "brand-dark": "#003F91",
        "brand-soft": "#215BA8"
      }
    }
  },
  plugins: [require("@tailwindcss/typography")]
};

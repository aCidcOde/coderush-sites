module.exports = {
  content: [
    "./index.php",
    "./blog/**/*.php",
    "./codafacil/**/*.php",
    "./inteligencia-artificial/**/*.php",
    "./wordpress/**/*.php",
    "./2023/**/*.php",
    "./2026/**/*.php"
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

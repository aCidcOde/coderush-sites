module.exports = {
  content: [
    "./sistemavendadireta/index.php",
    "./sistemavendadireta/blog/**/*.php",
    "./sistemavendadireta/inteligencia-artificial/**/*.php",
    "./sistemavendadireta/wordpress/**/*.php",
    "./sistemavendadireta/20*/**/*.php"
  ],
  safelist: [
    "italic",
    "border-l-4",
    "border-white",
    "bg-white/10",
    "text-white",
    "decoration-white/40",
    "underline-offset-4"
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

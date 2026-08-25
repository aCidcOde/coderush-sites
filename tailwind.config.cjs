module.exports = {
  content: [
    "./sistemavendadireta/index.php",
    // inc/ gera HTML (faixa de promocao, cases): sem varrer aqui, as classes
    // usadas nesses includes nunca entram no CSS e o layout quebra em producao
    "./sistemavendadireta/inc/**/*.php",
    "./sistemavendadireta/blog/**/*.php",
    "./sistemavendadireta/cases/**/*.php",
    "./sistemavendadireta/oferta/**/*.php",
    "./sistemavendadireta/simulador/**/*.php",
    "./sistemavendadireta/sistema-mmn/**/*.php",
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

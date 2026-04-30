module.exports = {
  content: [
    "./fluxointeligenteia/**/*.html",
    "./fluxointeligenteia/**/*.php"
  ],
  safelist: [
    "italic",
    "border-l-4",
    "border-emerald-400",
    "bg-emerald-400/10",
    "text-emerald-400",
    "decoration-emerald-400/40",
    "underline-offset-4"
  ],
  theme: {
    extend: {
      colors: {
        brand: "#059669",
        "brand-dark": "#064e3b"
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"],
        heading: ["Montserrat", "sans-serif"]
      }
    }
  },
  plugins: []
};

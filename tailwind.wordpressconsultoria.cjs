module.exports = {
  content: [
    "./wordpressconsultoria/**/*.html",
    "./wordpressconsultoria/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        brand: "#21759b",
        "brand-dark": "#0d4060"
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"],
        heading: ["Montserrat", "sans-serif"]
      }
    }
  },
  plugins: []
};

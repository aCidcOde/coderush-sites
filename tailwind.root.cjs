module.exports = {
  content: [
    "./index.php",
    "./index.html",
    "./blog/**/*.php",
    "./20*/**/*.php"
  ],
  theme: {
    extend: {
      colors: {
        brand: "#004AAD",
        "brand-dark": "#001f4d"
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"],
        heading: ["Montserrat", "sans-serif"]
      }
    }
  },
  plugins: []
};

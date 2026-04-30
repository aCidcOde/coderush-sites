module.exports = {
  content: [
    "./index.php",
    "./index.html",
    "./blog/**/*.php",
    "./20*/**/*.php"
  ],
  safelist: [
    "italic",
    "border-l-4",
    "border-blue-400",
    "bg-blue-400/10",
    "text-blue-400",
    "decoration-blue-400/40",
    "underline-offset-4"
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

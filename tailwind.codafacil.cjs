module.exports = {
  content: [
    "./codafacil/**/*.php",
    "./codafacil/**/*.html"
  ],
  safelist: [
    "italic",
    "border-l-4",
    "border-sky-300",
    "bg-sky-300/10",
    "text-sky-300",
    "decoration-sky-300/40",
    "underline-offset-4"
  ],
  theme: {
    extend: {}
  },
  plugins: [require("@tailwindcss/typography")]
};

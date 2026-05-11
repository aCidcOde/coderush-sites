module.exports = {
  content: [
    "./fluxointeligenteia/**/*.html",
    "./fluxointeligenteia/**/*.php",
    "./automation/blog-bot/lib/publisher.js"
  ],
  safelist: [
    "italic",
    "border-l-4",
    "border-emerald-400",
    "bg-emerald-400/10",
    "text-emerald-400",
    "decoration-emerald-400/40",
    "underline-offset-4",
    "overflow-hidden",
    "rounded-2xl",
    "rounded-3xl",
    "border-white/15",
    "bg-white/5",
    "h-44",
    "w-full",
    "object-cover"
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

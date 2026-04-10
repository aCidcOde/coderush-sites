// Extracted from sistemavendadireta/inteligencia-artificial/index.php block 1.
document.addEventListener("DOMContentLoaded", function () {
      var containers = document.querySelectorAll(".lottie-box[data-lottie-src]");
      if (!containers.length || !window.lottie) {
        return;
      }

      var reduceMotion = window.matchMedia && window.matchMedia("(prefers-reduced-motion: reduce)").matches;
      var isSmallScreen = window.matchMedia && window.matchMedia("(max-width: 767px)").matches;
      var startAnimation = function (container) {
        if (container.dataset.lottieLoaded === "1") {
          return;
        }

        var src = container.getAttribute("data-lottie-src");
        if (!src) {
          return;
        }

        if (container.getAttribute("data-lottie-mobile") === "false" && isSmallScreen) {
          return;
        }

        window.lottie.loadAnimation({
          container: container,
          renderer: "svg",
          loop: !reduceMotion,
          autoplay: !reduceMotion,
          path: src
        });

        container.dataset.lottieLoaded = "1";
      };

      if (!("IntersectionObserver" in window)) {
        containers.forEach(startAnimation);
        return;
      }

      var observer = new IntersectionObserver(function (entries, currentObserver) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) {
            return;
          }
          startAnimation(entry.target);
          currentObserver.unobserve(entry.target);
        });
      }, { rootMargin: "120px 0px" });

      containers.forEach(function (container) {
        observer.observe(container);
      });
    });

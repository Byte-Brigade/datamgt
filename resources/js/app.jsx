import "./bootstrap";
import "../css/app.css";

import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

import { ThemeProvider } from "@material-tailwind/react";

const appName =
  window.document.getElementsByTagName("title")[0]?.innerText || "PDBOM";

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) =>
    resolvePageComponent(
      `./Pages/${name}.jsx`,
      import.meta.glob("./Pages/**/*.jsx")
    ),
  setup({ el, App, props }) {
    const root = createRoot(el);
    const theme = {
      drawer: {
        defaultProps: {
          overlay: false,
          placement: "left",
          overlayProps: undefined,
          className: "",
          dismiss: undefined,
          onClose: undefined,
          transition: {
            type: "tween",
            duration: 0.3,
          },
        },
        styles: {
          base: {
            drawer: {
              position: "fixed",
              zIndex: "z-[9999]",
              pointerEvents: "pointer-events-auto",
              backgroundColor: "bg-white",
              width: "w-64",
            },

          },
        },
      },
    };
    root.render(
      <ThemeProvider value={theme}>
        <App {...props} />
      </ThemeProvider>
    );
  },
  progress: {
    color: "#4B5563",
  },
});

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
      list: {
        defaultProps: {
          ripple: true,
          className: "",
        },
        styles: {
          base: {
            item: {
              selected: {
                bg: "bg-blue-gray-50/75",
                color: "text-blue-gray-700",
              },
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

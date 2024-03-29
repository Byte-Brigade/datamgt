import "./bootstrap";
import "../css/app.css";

import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";

import { ThemeProvider } from "@material-tailwind/react";
import { FormProvider } from "./Components/Context/FormProvider";

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
      accordion: {
        defaultProps: {
          icon: undefined,
          className: "",
          animate: {
            unmount: {},
            mount: {},
          },
          disabled: false,
        },
      },
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
        <FormProvider>
        <App {...props} />
      </FormProvider>
      </ThemeProvider >
    );
  },
  progress: {
    color: "#4B5563",
  },
});

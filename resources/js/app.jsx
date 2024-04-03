import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createRoot } from "react-dom/client";

import { ThemeProvider } from "@material-tailwind/react";
import { LocalizationProvider } from "@mui/x-date-pickers";
import { AdapterDayjs } from "@mui/x-date-pickers/AdapterDayjs";
import { FormProvider } from "./Components/Context/FormProvider";

import { ToastContainer } from "react-toastify";
import 'react-toastify/dist/ReactToastify.css';

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
      <LocalizationProvider dateAdapter={AdapterDayjs}>
        <ThemeProvider value={theme}>
          <FormProvider>
            <ToastContainer
              position="bottom-right"
              autoClose={3000}
              hideProgressBar={false}
              newestOnTop
              closeOnClick
              rtl={false}
              pauseOnFocusLoss
              draggable
              pauseOnHover
              theme="colored"
              limit={3}
            />
            <App {...props} />
          </FormProvider>
        </ThemeProvider>
      </LocalizationProvider>
    );
  },
  progress: {
    color: "#4B5563",
  },
});

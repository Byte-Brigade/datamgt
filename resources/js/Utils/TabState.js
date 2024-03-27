import { usePage } from "@inertiajs/react";
import { useEffect, useState } from "react";

const tabState = (values) => {
  const [active, setActive] = useState(values[0]);

  const [params, setParams] = useState(() => {
    const { slug } = route().params;

    const urlParams = new URLSearchParams(window.location.search);
    const initialValue = urlParams.get("tab") || values[0];
    return { slug, value: initialValue };
  });

  const page = usePage();
  console.log(route().params);

  useEffect(() => {
    if (params.slug) {
      const newPath = `${params.slug}`;
      window.history.replaceState(null, "", `${newPath}?tab=${params.value}`);
    }
    if (route().params.branch) {
      window.history.replaceState(
        null,
        "",
        `?branch=${route().params.branch}&tab=${params.value}`
      );
    } else {
      window.history.replaceState(null, "", `?tab=${params.value}`);
    }

    setActive(params.value);
  }, [params]);

  const handleTabChange = (tab) => {
    setActive(tab);
    setParams((prev) => ({ ...prev, value: tab }));
  };

  return {
    params,
    active,
    handleTabChange,
  };
};

export { tabState };

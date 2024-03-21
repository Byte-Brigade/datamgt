import { useEffect, useState } from "react";

const tabState = (values) => {
  const [active, setActive] = useState(values[0]);

  const [params, setParams] = useState(() => {
    const { slug } = route().params;

    const urlParams = new URLSearchParams(window.location.search);
    const initialValue = urlParams.get("tab") || values[0];
    return { slug, value: initialValue };
  });

  useEffect(() => {
    if (params.slug) {
      const newPath = `${params.slug}`;
      window.history.replaceState(null, "", `${newPath}?tab=${params.value}`);
    }
    window.history.replaceState(null, "", `?tab=${params.value}`);

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
  }
};

export { tabState };

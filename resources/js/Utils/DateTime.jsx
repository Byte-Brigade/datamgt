import { useEffect, useState } from "react";

function DateTime() {
  // const [date, setDate] = useState(new Date());

  // useEffect(() => {
  //   const timer = setInterval(() => setDate(new Date()), 1000);

  //   return function cleanup() {
  //     clearInterval(timer);
  //   };
  // }, []);

  const date = new Date();

  const options = {
    weekday: "long",
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "numeric",
  };

  return date.toLocaleString("id-ID", options);
}

export { DateTime };

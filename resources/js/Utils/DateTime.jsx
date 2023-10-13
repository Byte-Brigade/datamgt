import { useEffect, useState } from "react";

export const DateTime = ({ className }) => {
  const [date, setDate] = useState(new Date());

  useEffect(() => {
    const timer = setInterval(() => setDate(new Date()), 1000);

    return function cleanup() {
      clearInterval(timer);
    };
  });

  const options = {
    weekday: "long",
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "numeric",
    minute: "numeric",
    second: "numeric",
  };

  return (
    <div className={className}>
      <p>{date.toLocaleString("id-ID", options)}</p>
    </div>
  );
};

export default DateTime;

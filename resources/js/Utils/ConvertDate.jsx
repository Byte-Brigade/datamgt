const ConvertDate = (date) => {
  if (date === "-") return "-";
  if (date === null) return "-";
  const d = new Date(date);
  const options = {
    day: "numeric",
    month: "short",
    year: "numeric",
  };
  return d.toLocaleDateString("id-ID", options);
};

export { ConvertDate };

import { useEffect, useState } from "react";

const YearPicker = () => {
  const currentYear = new Date().getFullYear();
  const [selectedYear, setSelectedYear] = useState(currentYear);
  const [startYear, setStartYear] = useState(currentYear - 10);
  const [endYear, setEndYear] = useState(currentYear + 10);

  const years = Array.from(
    { length: endYear - startYear + 1 },
    (_, index) => startYear + index
  );

  useEffect(() => {
    // Do any additional logic based on the selected year if needed
  }, [selectedYear]);

  const handleChange = (event) => {
    const selected = parseInt(event.target.value, 10);
    setSelectedYear(selected);
    // Do any other logic based on the selected year if needed
  };

  return (
    <select
      className="border p-2 rounded-md w-fit"
      value={selectedYear}
      onChange={handleChange}
    >
      {years.map((year) => (
        <option key={year} value={year}>
          {year}
        </option>
      ))}
    </select>
  );
};

export default YearPicker;

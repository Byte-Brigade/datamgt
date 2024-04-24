import { useState } from "react";

const MonthPicker = ({ onDateChange }) => {
  // Initial state is null to indicate no selection
  const [date, setDate] = useState(null);

  const handleChange = (event) => {
    const newDate = event.target.value;
    setDate(newDate);

    // Extracting the year and month from the input value
    const [year, month] = newDate.split("-").map(Number);
    (newDate);

    // Propagating the date change up to the parent component
    onDateChange({ month, year });
  };

  // Here we check if date is null before setting the value property
  const inputValue = date ? date : "";

  return (
    <div className="flex items-center justify-center">
      <input
        type="month"
        className="border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
        value={inputValue} // Only set a value if date is not null
        onChange={handleChange}
        placeholder="Pilih Periode"
      />
    </div>
  );
};

export default MonthPicker;

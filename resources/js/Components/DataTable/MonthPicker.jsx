import { useState } from 'react';

const MonthPicker = ({ onDateChange }) => {
  // Initial state is set to the current month and year
  const [date, setDate] = useState(new Date().toISOString().slice(0, 7));

  const handleChange = (event) => {
    const newDate = event.target.value;
    setDate(newDate);

    // Extracting the year and month from the input value
    const [year, month] = newDate.split('-').map(Number);

    // Propagating the date change up to the parent component
    onDateChange({ month, year });
  };

  return (
    <div className="flex items-center justify-center">
      <input
        type="month"
        className="bg-gray-50 border border-gray-300 text-gray-900 sm:text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
        value={date}
        onChange={handleChange}
      />
    </div>
  );
};

export default MonthPicker;

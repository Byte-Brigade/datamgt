import { Option, Select } from "@material-tailwind/react";

const YearPicker = ({
  startYear,
  endYear,
  label,
  value,
  processing,
  onChange,
}) => {
  const currentYear = new Date().getFullYear();
  const start = startYear || currentYear;
  const end = endYear || currentYear + 1;

  const years = Array.from(
    { length: end - start + 1 },
    (_, index) => start + index
  );

  return (
    <Select
      label={label}
      value={currentYear || value}
      disabled={processing}
      onChange={onChange}
      size="md"
    >
      {years.map((year) => (
        <Option key={year} value={year}>
          {year}
        </Option>
      ))}
    </Select>
  );
};

export default YearPicker;

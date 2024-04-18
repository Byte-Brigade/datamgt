import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import { CogIcon } from "@heroicons/react/24/outline";
import { IconButton } from "@material-tailwind/react";
import { DatePicker } from "@mui/x-date-pickers";

export default function Configuration({
  perPage,
  handlePerPage,
  handleSearch,
  toggleOpen,
  toggleOpenSetting,
  periodic,
  datePicker = { year: true, month: false, day: false },
  handleYearChange,
  year,
  handleMonthChange,
  month,
  handleDateChange,
  date,
}) {
  return (
    <div className="flex flex-col">
      <div className="flex items-center justify-between mb-2">
        <div className="flex flex-col w-72">
          <div className="flex items-center gap-x-2">
            <label htmlFor="perpage">Show</label>
            <select
              name="perpage"
              id="perpage"
              className="rounded-lg form-select focus:border-indigo-500 border-gray-300 shadow-sm"
              value={perPage}
              onChange={(e) => handlePerPage(e.target.value)}
            >
              <option value="15">15</option>
              <option value="30">30</option>
              <option value="45">45</option>
              <option value="60">60</option>
              <option value="All">All</option>
            </select>
            <label htmlFor="perpage">entries</label>
          </div>
        </div>
        <div className="flex flex-col">
          <div className="flex gap-2">
            <div className="flex items-center gap-2">
              <InputLabel htmlFor="search">Search : </InputLabel>
              <TextInput
                type="search"
                name="search"
                id="search"
                onChange={(e) => handleSearch(e.target.value)}
              />
            </div>

            <IconButton onClick={toggleOpen} className="bg-green-600">
              <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                strokeWidth="1.5"
                stroke="currentColor"
                className="w-6 h-6"
              >
                <path
                  strokeLinecap="round"
                  strokeLinejoin="round"
                  d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"
                />
              </svg>
            </IconButton>
            <IconButton onClick={toggleOpenSetting} className="bg-green-600">
              <CogIcon className="w-5 h-5" />
            </IconButton>
          </div>
        </div>
      </div>
      <div>
        {periodic && (
          <div className="flex justify-between">
            {datePicker.year && (
              <div className="z-50 flex items-center justify-end gap-x-2">
                <span>Tahun</span>
                <DatePicker
                  value={year}
                  onChange={handleYearChange}
                  openTo="year"
                  views={["year"]}
                  slotProps={{ textField: { size: "small" } }}
                  className="bg-white"
                />
              </div>
            )}
            {datePicker.month && (
              <div className="z-50 flex items-center justify-end gap-x-2">
                <span>Bulan</span>
                <DatePicker
                  value={month}
                  onChange={handleMonthChange}
                  openTo="month"
                  views={["year", "month"]}
                  slotProps={{ textField: { size: "small" } }}
                  className="bg-white"
                />
              </div>
            )}
            {datePicker.day && (
              <div className="z-50 flex items-center justify-end gap-x-2">
                <span>Tanggal</span>
                <DatePicker
                  value={date}
                  onChange={handleDateChange}
                  openTo="day"
                  views={["year", "month", "day"]}
                  slotProps={{ textField: { size: "small" } }}
                  className="bg-white"
                />
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}

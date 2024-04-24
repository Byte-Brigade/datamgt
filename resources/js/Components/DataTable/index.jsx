import { ChevronDownIcon, ChevronUpIcon } from "@heroicons/react/24/solid";
import { Checkbox, Collapse, Typography } from "@material-tailwind/react";
import axios from "axios";
import { debounce } from "lodash";
import { useEffect, useRef, useState } from "react";
import { useFormContext } from "../Context/FormProvider";
import Paginator from "./Paginator";
import { Configuration, Filters, Loading, NoData, TableRow } from "./Partials";

const SORT_ASC = "asc";
const SORT_DESC = "desc";

export default function DataTable({
  columns = [
    {
      name: "",
      value: "",
      field: "",
      type: "",
      className: "",
      render: (any) => any,
    },
  ],
  fetchUrl,
  refreshUrl = false,
  dataArr,
  className = "w-full",
  component = [],
  datePicker = { year: true, month: false, day: false },
  periodic = false,
  parameters = {},
  bordered = false,
  configuration = true,
  headings,
  submitUrl = {},
  fixed = false,
}) {
  const [data, setData] = useState([]);
  const [perPage, setPerPage] = useState(15);
  const [sortColumn, setSortColumn] = useState();
  const [sortOrder, setSortOrder] = useState("asc");
  const [search, setSearch] = useState("");
  const [pagination, setPagination] = useState({});
  const [currentPage, setCurrentPage] = useState(1);
  const [loading, setLoading] = useState(false);
  const [open, setOpen] = useState(false);
  const [openSetting, setOpenSetting] = useState(false);
  const [fixedTable, setFixedTable] = useState(fixed);
  const [remarks, setRemarks] = useState({});

  const {
    form,
    setInitialData,
    handleFormSubmit,
    setUrl,
    setId,
    isRefreshed,
    selected,
    setSelected,
    filterData,
    setFilterData,
    datePickerValue,
    setDatePickerValue,
  } = useFormContext();

  // filters
  const [filters, setFilters] = useState([]);
  const [clearFilter, setClearFilter] = useState(false);

  const toggleOpen = () => setOpen((cur) => !cur);

  const [dateRange, setDateRange] = useState({
    startDate: null,
    endDate: null,
  });
  const [date, setDate] = useState(null);
  const [year, setYear] = useState(null);
  const [month, setMonth] = useState(null);

  const handleDateChange = (newValue) => {
    ("newValue:", newValue);
    setDate(newValue);
    setYear(null);
    setMonth(null);
    setDatePickerValue(newValue);
  };
  const handleYearChange = (newValue) => {
    ("newValue:", newValue);
    setDate(null);
    setDatePickerValue({ $y: newValue["$y"] });
    setMonth(null);
    setYear(newValue);
  };
  const handleMonthChange = (newValue) => {
    ("newValue:", newValue);
    setDate(null);
    setYear(null);
    setMonth(newValue);
    setDatePickerValue({ $y: newValue["$y"], $M: newValue["$M"] });
  };

  const handleSort = (column) => {
    if (columns === sortColumn) {
      sortOrder === SORT_ASC ? setSortOrder(SORT_DESC) : setSortOrder(SORT_ASC);
    } else {
      setSortColumn(column);
      setSortOrder(SORT_ASC);
    }
  };

  const handleSearch = useRef(
    debounce((query) => {
      setSearch(query);
      setCurrentPage(1);
      setSortOrder(SORT_ASC);
      setSortColumn(columns[0].field);
    }, 500)
  ).current;

  const handleFilter = () => {
    fetchData(1);
  };

  const handleCheckbox = async (filter, field) => {
    setFilters((prevFilter) =>
      prevFilter.includes(filter)
        ? prevFilter.filter((c) => c !== filter)
        : [...prevFilter, filter]
    );
  };
  const handleClearFilter = () => {
    setFilters([]);
    setFilterData([]);

    setClearFilter((prevClear) => !prevClear);
  };
  const handleCheckboxData = (filter, field) => {
    setFilterData((prevFilter) => {
      const updatedFilter = { ...prevFilter };
      if (!updatedFilter.hasOwnProperty(field)) {
        updatedFilter[field] = [filter];
      } else {
        if (!updatedFilter[field].includes(filter)) {
          updatedFilter[field].push(filter);
        } else {
          updatedFilter[field] = updatedFilter[field].filter(
            (item) => item !== filter
          );
        }
      }

      return updatedFilter;
    });
  };

  const handlePerPage = (perPage) => {
    setCurrentPage(1);
    setPerPage(perPage);
  };

  const fetchData = async (currPage = 0) => {
    setLoading(true);
    const params = {
      ...parameters,
      page: currPage > 0 ? currPage : currentPage,
      perpage: perPage,
      sort_field: sortColumn,
      sort_order: sortOrder,
      search,
      ...dateRange,
      ...datePickerValue,
      ...filterData,
    };

    if (fetchUrl) {
      const { data } = await axios.get(fetchUrl, {
        params,
        withCredentials: true,
      });
      setData(
        data.data instanceof Object ? Object.values(data.data) : data.data
      );
      setPagination(data.meta ? data.meta : data);
      setLoading(false);
      (data.data);
    }
    if (dataArr) {
      (dataArr);
      setData(dataArr);
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
    setUrl(submitUrl.url);
    if (submitUrl.id !== undefined) {
      setId(submitUrl.id);
    }
  }, [
    perPage,
    sortColumn,
    sortOrder,
    search,
    currentPage,
    refreshUrl,
    clearFilter,
    isRefreshed,
    configuration,
    dateRange,
    datePickerValue,
  ]);

  const getNestedValue = (obj, field) => {
    const keys = field.split(".");
    let value = obj;

    for (const key of keys) {
      if (value !== null && value.hasOwnProperty(key)) {
        value = value[key];
      } else {
        value = null;
        break;
      }
    }

    return value;
  };

  const convertDate = (date) => {
    if (date === null) return "-";
    const d = new Date(date);
    const options = {
      day: "numeric",
      month: "short",
      year: "numeric",
    };
    return d.toLocaleDateString("id-ID", options);
  };

  const toggleOpenSetting = () => setOpenSetting((cur) => !cur);

  const handleTableSettings = () => {
    setFixedTable((cur) => !cur);
  };

  return (
    <div>
      {configuration && (
        <Configuration
          perPage={perPage}
          handlePerPage={handlePerPage}
          handleSearch={handleSearch}
          toggleOpen={toggleOpen}
          toggleOpenSetting={toggleOpenSetting}
          periodic={periodic}
          handleYearChange={handleYearChange}
          year={year}
          handleMonthChange={handleMonthChange}
          month={month}
          handleDateChange={handleDateChange}
          date={date}
        />
      )}

      <Filters
        open={open}
        columns={columns}
        filters={filters}
        component={component}
        filterData={filterData}
        handleCheckbox={handleCheckbox}
        handleCheckboxData={handleCheckboxData}
        handleFilter={handleFilter}
        handleClearFilter={handleClearFilter}
      />

      <div id="settings">
        <Collapse open={openSetting}>
          <div className="flex justify-between w-full mx-auto my-2 bg-slate-200 p-2 rounded-lg shadow-inner">
            <div className="flex flex-col flex-wrap">
              <span className="ml-3 font-medium text-lg">Settings</span>
              <div className="flex flex-wrap">
                <Checkbox
                  checked={fixedTable}
                  onChange={handleTableSettings}
                  label={
                    <Typography color="black" className="font-medium">
                      Freeze Header
                    </Typography>
                  }
                />
              </div>
            </div>
          </div>
        </Collapse>
      </div>

      <div
        className={`relative overflow-x-auto border-2 rounded-lg border-slate-200 ${
          fixedTable ? "max-h-96" : "h-full"
        }`}
      >
        <table className={`${className} text-sm leading-3 bg-white z-0`}>
          <thead className="sticky top-0  border-b-2 table-fixed border-slate-200">
            {headings && (
              <tr
                className={`[&>th]:p-2 bg-slate-100 ${
                  bordered &&
                  "divide-x-2 divide-slate-200 border-b-2 border-slate-200"
                }`}
              >
                {headings.map((column, i) => (
                  <th key={i} rowSpan={column.rowSpan} colSpan={column.colSpan}>
                    <div>{column.name}</div>
                  </th>
                ))}
              </tr>
            )}

            <tr
              className={`[&>th]:p-2 bg-slate-100 ${
                bordered && "divide-x-2 divide-slate-200"
              }`}
            >
              <th className={"text-center"}>No</th>
              {columns.map((column, i) => (
                <th
                  className={
                    column.freeze &&
                    `sticky z-20 left-0 bg-slate-100 border-b-2 `
                  }
                  key={i}
                >
                  {column.sortable === true ? (
                    <div
                      className="cursor-pointer hover:underline"
                      onClick={(e) => handleSort(column.field)}
                    >
                      <div className="flex items-center gap-x-1">
                        {column.name}
                        <span className="flex flex-col gap-y-1">
                          <ChevronUpIcon
                            className={`${
                              sortOrder === SORT_ASC &&
                              column.field === sortColumn
                                ? "text-slate-900"
                                : "text-gray-400"
                            } w-3 h-3`}
                          />
                          <ChevronDownIcon
                            className={`${
                              sortOrder === SORT_DESC &&
                              column.field === sortColumn
                                ? "text-slate-900"
                                : "text-gray-400"
                            } w-3 h-3`}
                          />
                        </span>
                      </div>
                    </div>
                  ) : (
                    <div>{column.name}</div>
                  )}
                </th>
              ))}
            </tr>
          </thead>
          <tbody className="overflow-y-auto">
            {loading ? (
              <Loading length={columns.length + 1} />
            ) : data.length === 0 ? (
              <NoData length={columns.length + 1} />
            ) : (
              <>
                {data.map((data, index) => (
                  <TableRow
                    key={index}
                    className={`[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200 ${
                      bordered && "divide-x-2 divide-slate-200"
                    }`}
                    // isSelected={selectedRows.includes(index)}
                    // onClick={(event) => handleRowClick(event, index)}
                  >
                    <td className="text-center">
                      {Object.keys(pagination).length === 0 ? (
                        <p className="py-3">{index + 1}</p>
                      ) : (
                        <p className="py-3">{pagination.from + index}</p>
                      )}
                    </td>
                    {columns.map((column, id) =>
                      column.field ? (
                        column.field === "action" ||
                        column.field === "detail" ? (
                          <td
                            key={column.field}
                            colSpan={column.colSpan}
                            className={`${column.className} ${
                              column.freeze && "sticky left-0 bg-white"
                            }`}
                          >
                            {column.render(data)}
                          </td>
                        ) : column.remark ? (
                          <td>
                            <Checkbox
                              key={id}
                              checked={remarks[data.id]}
                              color="light-blue"
                              onChange={(e) =>
                                handleRowCheckboxChange(e, data.id, column.url)
                              }
                            />
                          </td>
                        ) : (
                          <td
                            key={
                              column.type === "custom"
                                ? column.key
                                : column.field
                            }
                            colSpan={column.colSpan}
                            className={`${column.className} ${
                              column.freeze && "sticky left-0 bg-white"
                            }`}
                          >
                            {column.type === "date"
                              ? convertDate(getNestedValue(data, column.field))
                              : column.type === "custom"
                              ? column.render(data) && column.render(data) != 0
                                ? column.render(data)
                                : "-"
                              : getNestedValue(data, column.field) || "-"}
                          </td>
                        )
                      ) : (
                        <td
                          key={id}
                          className={column.className}
                          colSpan={column.colSpan}
                        >
                          {column.value || "-"}
                        </td>
                      )
                    )}
                  </TableRow>
                ))}
                {columns.filter((column) => column.agg !== undefined).length >
                  0 && (
                  <tr
                    className={`[&>td]:p-2 bg-slate-100 hover:bg-slate-200 border-b border-slate-200 ${
                      bordered && "divide-x-2 divide-slate-200"
                    }`}
                  >
                    <td className="font-bold text-center">Subtotal</td>
                    {columns.map((column, index) =>
                      column.agg === "sum" ? (
                        <td
                          key={index}
                          className={`font-bold ${column.className}`}
                        >
                          {column.type === "custom"
                            ? column.format === "currency"
                              ? data
                                  .reduce((total, acc) => {
                                    return (
                                      total +
                                      parseInt(
                                        column.render(acc).replace(/\D/g, ""),
                                        10
                                      )
                                    );
                                  }, 0)
                                  .toLocaleString("id-ID")
                              : data.reduce((total, acc) => {
                                  return (
                                    total +
                                    parseInt(
                                      column.render(acc).replace(/\D/g, ""),
                                      10
                                    )
                                  );
                                }, 0)
                            : data.reduce((total, acc) => {
                                return total + acc[column.field];
                              }, 0)}
                        </td>
                      ) : column.agg === "count" ? (
                        <td
                          key={index}
                          className={`font-bold ${column.className}`}
                        >
                          {column.type === "custom"
                            ? data.reduce((total, acc) => {
                                return total + parseInt(column.render(acc));
                              }, 0)
                            : data.reduce((total, acc) => {
                                return total + acc[column.field].length;
                              }, 0)}
                        </td>
                      ) : (
                        <td></td>
                      )
                    )}
                  </tr>
                )}
              </>
            )}
          </tbody>
        </table>
      </div>
      {data.length > 0 && !loading && (
        <Paginator
          pagination={pagination}
          pageChanged={(page) => setCurrentPage(page)}
          totalItems={data.length}
        />
      )}
    </div>
  );
}

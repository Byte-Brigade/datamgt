import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import { ChevronDownIcon, ChevronUpIcon } from "@heroicons/react/24/solid";
import axios from "axios";
import { debounce } from "lodash";
import { useEffect, useRef, useState } from "react";
import Paginator from "./Paginator";
import {
  IconButton,
  Collapse,
  Card,
  CardBody,
  Typography,
  Select,
  Option,
  Checkbox,
  Popover,
  PopoverHandler,
  PopoverContent,
} from "@material-tailwind/react";

const SORT_ASC = "asc";
const SORT_DESC = "desc";

export default function DataTable({
  columns = { name: "", value: "", field: "", type: "", render: (any) => any },
  fetchUrl,
  refreshUrl = false,
  dataArr,
}) {
  const [data, setData] = useState([]);
  const [perPage, setPerPage] = useState(10);
  const [sortColumn, setSortColumn] = useState(columns[0].field);
  const [sortOrder, setSortOrder] = useState("asc");
  const [search, setSearch] = useState("");
  const [pagination, setPagination] = useState({});
  const [currentPage, setCurrentPage] = useState(1);
  const [filters, setFilters] = useState([]);
  const [filterData, setFilterData] = useState([]);
  const [selected, setSelected] = useState("0");

  const [loading, setLoading] = useState(false);

  const [open, setOpen] = useState(false);

  const toggleOpen = () => setOpen((cur) => !cur);

  const handleSort = (column) => {
    if (column === sortColumn) {
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
      setSortColumn(columns[0].name);
    }, 500)
  ).current;

  const handleCheckbox = (filter) => {
    console.log(filters);
    setFilters((prevFilter) =>
      prevFilter.includes(filter)
        ? prevFilter.filter((c) => c !== filter)
        : [...prevFilter, filter]
    );
  };

  const handleCheckboxData = (filter) => {
    console.log(filterData);
    setFilterData((prevFilter) =>
      prevFilter.includes(filter)
        ? prevFilter.filter((c) => c !== filter)
        : [...prevFilter, filter]
    );
  };

  const handlePerPage = (perPage) => {
    setCurrentPage(1);
    setPerPage(perPage);
  };

  const fetchData = async () => {
    setLoading(true);
    const params = {
      page: currentPage,
      perpage: perPage,
      sort_field: sortColumn,
      sort_order: sortOrder,
      search,
      filters,
      filterData,
    };

    if (fetchUrl) {
      const { data } = await axios.get(fetchUrl, { params });
      console.log(data);
      setData(data.data);
      setPagination(data.meta);
      setLoading(false);
    }

    if (dataArr) {
      console.log(dataArr);
      setData(dataArr);
      setLoading(false);
    }
  };

  useEffect(() => {
    fetchData();
  }, [
    perPage,
    sortColumn,
    sortOrder,
    search,
    currentPage,
    refreshUrl,
    filters,
    // filterData,
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

  return (
    <>
      <div className="flex items-center justify-between mb-4">
        <div className="flex items-center gap-x-2">
          Show
          <select
            name="perpage"
            id="perpage"
            className="rounded-lg form-select"
            value={perPage}
            onChange={(e) => handlePerPage(e.target.value)}
          >
            <option value="10">10</option>
            <option value="20">20</option>
            <option value="50">50</option>
            <option value="100">100</option>
          </select>
          entries
        </div>
        <div className="flex gap-2">
          <div className="flex items-center gap-2">
            <InputLabel htmlFor="search">Search : </InputLabel>
            <TextInput
              type="search"
              name="search"
              onChange={(e) => handleSearch(e.target.value)}
            />
          </div>

          <IconButton onClick={toggleOpen}>
            <svg
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="1.5"
              stroke="currentColor"
              class="w-6 h-6"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z"
              />
            </svg>
          </IconButton>
        </div>
      </div>
      <div>
        <Collapse open={open}>
          <div className="flex flex-col my-4 mx-auto w-full">
            <span className="ml-3">category</span>

            <div className="flex flex-wrap">
              {columns.map((column, i) => {
                if (column.name !== "Action") {
                  return (
                    <Checkbox
                      label={column.name}
                      key={column.field}
                      checked={filters.includes(column.field)}
                      value={column.field}
                      onChange={(e) => handleCheckbox(e.target.value)}
                    />
                  );
                }
              })}
            </div>

            <div className="flex flex-wrap">
              <select
                label="Branch"
                onChange={(e) => setSelected(e.target.value)}
              >
                <option value="0">All</option>
                {columns
                  .filter((column) => column.filterable)
                  .map((column, i) => (
                    <option key={i} value={`${column.field}`}>
                      {column.name}
                    </option>
                  ))}
              </select>
              {!loading
                ? columns
                    .filter((column) => column.filterable)
                    .map((column, i) => {
                      if (data.length > 0 && column.field === selected) {
                        const uniqueValues = new Set(
                          data.map((item) => getNestedValue(item, column.field))
                        );

                        const filteredData = Array.from(uniqueValues).map(
                          (uniqueValue) =>
                            data.find(
                              (item) =>
                                getNestedValue(item, column.field) ===
                                uniqueValue
                            )
                        );
                        return filteredData.map((data) =>
                          column.field ? (
                            column.field === "action" ? (
                              <td
                                key={column.field}
                                className={column.className}
                              >
                                {column.render(data)}
                              </td>
                            ) : (
                              <Checkbox
                                onChange={(e) =>
                                  handleCheckboxData(e.target.value)
                                }
                                label={
                                  column.type === "date"
                                    ? convertDate(
                                        getNestedValue(data, column.field)
                                      )
                                    : column.type === "custom"
                                    ? column.render(data)
                                    : getNestedValue(data, column.field) || "-"
                                }
                                key={
                                  column.type === "date"
                                    ? convertDate(
                                        getNestedValue(data, column.field)
                                      )
                                    : column.type === "custom"
                                    ? column.render(data)
                                    : getNestedValue(data, column.field) || "-"
                                }
                                className={column.className}
                                value={
                                  column.type === "date"
                                    ? convertDate(
                                        getNestedValue(data, column.field)
                                      )
                                    : column.type === "custom"
                                    ? column.render(data)
                                    : getNestedValue(data, column.field) || "-"
                                }
                              />
                            )
                          ) : (
                            <td key={id} className={column.className}>
                              {column.value || "-"}
                            </td>
                          )
                        );
                      }
                    })
                : console.log("a")}
            </div>
          </div>
        </Collapse>
      </div>
      <div className="relative overflow-x-auto border-2 rounded-lg border-slate-200">
        <table className="w-full text-sm">
          <thead className="border-b-2 border-slate-200">
            <tr className="[&>th]:p-2 bg-slate-100">
              <th className="text-center">No</th>
              {columns
                .filter((column) =>
                  filters.length > 0 ? filters.includes(column.field) : true
                )
                .map((column, i) => (
                  <th key={column.name}>
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
          <tbody>
            {loading ? (
              <tr>
                <td
                  colSpan={columns.length + 1}
                  className="p-2 text-lg font-semibold text-center transition-colors duration-75 bg-slate-200 animate-pulse"
                >
                  Loading ...
                </td>
              </tr>
            ) : data.length === 0 ? (
              <tr>
                <td
                  colSpan={columns.length + 1}
                  className="p-2 text-lg font-semibold text-center bg-slate-200"
                >
                  Tidak ada data tersedia
                </td>
              </tr>
            ) : (
              data.map((data, index) => (
                <tr
                  key={index}
                  className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200"
                >
                  <td className="text-center">
                    {Object.keys(pagination).length === 0
                      ? index + 1
                      : pagination.from + index}
                  </td>
                  {columns
                    .filter((column) =>
                      filters.length > 0 ? filters.includes(column.field) : true
                    )
                    .map((column, id) =>
                      column.field ? (
                        column.field === "action" ? (
                          <td key={column.field} className={column.className}>
                            {column.render(data)}
                          </td>
                        ) : (
                          <td key={column.field} className={column.className}>
                            {column.type === "date"
                              ? convertDate(getNestedValue(data, column.field))
                              : column.type === "custom"
                              ? column.render(data)
                              : getNestedValue(data, column.field) || "-"}
                          </td>
                        )
                      ) : (
                        <td key={id} className={column.className}>
                          {column.value || "-"}
                        </td>
                      )
                    )}
                </tr>
              ))
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
    </>
  );
}

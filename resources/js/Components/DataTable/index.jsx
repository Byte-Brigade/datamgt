import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import { ChevronDownIcon, ChevronUpIcon } from "@heroicons/react/24/solid";
import axios from "axios";
import { debounce } from "lodash";
import { useEffect, useRef, useState } from "react";
import Paginator from "./Paginator";
import { usePage } from "@inertiajs/react";
import {
  IconButton,
  Collapse,
  Checkbox,
  Button,
} from "@material-tailwind/react";
import { CogIcon } from "@heroicons/react/24/outline";

const SORT_ASC = "asc";
const SORT_DESC = "desc";

export default function DataTable({
  columns = { name: "", value: "", field: "", type: "", render: (any) => any },
  fetchUrl,
  refreshUrl = false,
  dataArr,
  className = "w-full",
  component = [],
  footCols = { name: "", span: 0 },
}) {
  const [data, setData] = useState([]);
  const [perPage, setPerPage] = useState(15);
  const [sortColumn, setSortColumn] = useState(columns[0].field);
  const [sortOrder, setSortOrder] = useState("asc");
  const [search, setSearch] = useState("");
  const [pagination, setPagination] = useState({});
  const [currentPage, setCurrentPage] = useState(1);
  const [loading, setLoading] = useState(false);
  const [open, setOpen] = useState(false);
  const [openSetting, setOpenSetting] = useState(false);
  const [fixedTable, setFixedTable] = useState(false);

  // filters
  const [filters, setFilters] = useState([]);
  const [filterData, setFilterData] = useState({});
  const [clearFilter, setClearFilter] = useState(false);

  const toggleOpen = () => setOpen((cur) => !cur);

  const { auth } = usePage().props;
  const initialPermission = ["can edit", "can delete"];
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
      // Membuat salinan dari prevFilter
      const updatedFilter = { ...prevFilter };

      // Jika field belum ada dalam updatedFilter, tambahkan field dengan array filter ke dalam updatedFilter
      if (!updatedFilter.hasOwnProperty(field)) {
        updatedFilter[field] = [filter];
      } else {
        // Jika field sudah ada dalam updatedFilter, periksa apakah filter sudah ada dalam array tersebut
        // Jika belum, tambahkan filter ke dalam array filter
        if (!updatedFilter[field].includes(filter)) {
          updatedFilter[field].push(filter);
        } else {
          // Jika filter sudah ada dalam array filter, hapus filter dari array
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
      page: currPage > 0 ? currPage : currentPage,
      perpage: perPage,
      sort_field: sortColumn,
      sort_order: sortOrder,
      search,
      ...filterData,
    };

    if (fetchUrl) {
      const { data } = await axios.get(fetchUrl, { params });
      console.log(data);
      setData(
        data.data instanceof Object ? Object.values(data.data) : data.data
      );
      setPagination(data.meta ? data.meta : data);
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
    clearFilter,
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

  console.log(footCols)


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
            <option value="15">15</option>
            <option value="30">30</option>
            <option value="45">45</option>
            <option value="60">60</option>
          </select>
          entries
        </div>
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

          <IconButton onClick={toggleOpen}>
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
          <IconButton onClick={toggleOpenSetting}>
            <CogIcon className="w-5 h-5" />
          </IconButton>
        </div>
      </div>
      <div id="filters">
        <Collapse open={open}>
          <div className="flex justify-between w-full mx-auto my-2">
            <div className="flex flex-col flex-wrap">
              <span className="ml-3">Category</span>
              {columns
                .filter((column, index) => column.filterable)
                .map((column, id) => {
                  if (column.name !== "Action") {
                    return (
                      <Checkbox
                        label={column.name}
                        key={id}
                        checked={filters.includes(column.field)}
                        value={column.field}
                        onChange={(e) =>
                          handleCheckbox(
                            e.target.value,
                            column.component,
                            column.field
                          )
                        }
                      />
                    );
                  }
                })}
            </div>
            <div className="flex flex-wrap">
              {columns
                .filter((column) => column.filterable)
                .map((column, i) => {
                  if (component.length > 0 && filters.includes(column.field)) {
                    return component.map(({ data, field }, i) =>
                      column.field == field
                        ? data.map((item, index) => (
                            <Checkbox
                              onChange={(e) =>
                                handleCheckboxData(e.target.value, field)
                              }
                              checked={
                                filterData[field]
                                  ? filterData[field].includes(item)
                                  : false
                              }
                              label={item}
                              key={index}
                              className={column.className}
                              value={item}
                            />
                          ))
                        : ""
                    );
                  }
                })}
            </div>
            <div className="flex flex-col justify-center gap-y-2">
              <Button size="sm" onClick={handleClearFilter}>
                Clear
              </Button>
              <Button size="sm" color="green" onClick={handleFilter}>
                Filter
              </Button>
            </div>
          </div>
        </Collapse>
      </div>
      <div id="settings">
        <Collapse open={openSetting}>
          <div className="flex justify-between w-full mx-auto my-2">
            <div className="flex flex-col flex-wrap">
              <span className="ml-3">Settings</span>
              <div className="flex flex-wrap">
                <Checkbox
                  label="Fixed Table Height"
                  checked={fixedTable}
                  onChange={handleTableSettings}
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
        <table className={`${className} text-sm leading-3 bg-white`}>
          <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
            <tr className="[&>th]:p-2 bg-slate-100">
              <th className="text-center">No</th>
              {columns.map((column, i) => (
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
          <tbody className="overflow-y-auto">
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
                  {columns.map((column, id) =>
                    column.field ? (
                      column.field === "action" || column.field === "detail" ? (
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
          {footCols.length > 0 && (
            <tfoot className="sticky bottom-0 border-t-2 border-slate-200">
              <tr className="[&>th]:p-2 bg-slate-100">
                {footCols.map((col, index) => (
                  <th key={index} colSpan={col.span}>
                    {col.name}
                  </th>
                ))}
              </tr>
            </tfoot>
          )}
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

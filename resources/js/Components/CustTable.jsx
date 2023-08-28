import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import axios from "axios";
import { debounce } from "lodash";
import { useEffect, useRef, useState } from "react";
import Paginator from "./Paginator";

const SORT_ASC = "asc";
const SORT_DESC = "desc";

export default function CustTable({ columns, fetchUrl }) {
  const [data, setData] = useState([]);
  const [perPage, setPerPage] = useState(10);
  const [sortColumn, setSortColumn] = useState(columns[0].data);
  const [sortOrder, setSortOrder] = useState("asc");
  const [search, setSearch] = useState("");
  const [pagination, setPagination] = useState({});
  const [currentPage, setCurrentPage] = useState(1);

  const [loading, setLoading] = useState(false);

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
      setSortColumn(columns[0].field);
    }, 500)
  ).current;

  const handlePerPage = (perPage) => {
    setCurrentPage(1);
    setPerPage(perPage);
  };

  useEffect(() => {
    const fetchData = async () => {
      setLoading(true);
      const params = {
        page: currentPage,
        perpage: perPage,
        sort_field: sortColumn,
        sort_order: sortOrder,
        search,
      };

      const { data } = await axios.get(fetchUrl, { params });
      setData(data.data);
      setPagination(data.meta);
      setLoading(false);
    };

    fetchData();
  }, [perPage, sortColumn, sortOrder, search, currentPage]);
  return (
    <>
      <div className="flex items-center justify-between mb-4">
        <select
          name="perpage"
          id="perpage"
          className="rounded-lg bg-slate-100"
          value={perPage}
          onChange={(e) => handlePerPage(e.target.value)}
        >
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <div>
          <div className="flex items-center gap-2">
            <InputLabel htmlFor="search">Search : </InputLabel>
            <TextInput
              type="search"
              name="search"
              onChange={(e) => handleSearch(e.target.value)}
            />
          </div>
        </div>
      </div>
      <div className="relative overflow-x-auto border-2 rounded-lg border-slate-200">
        <table className="w-full text-sm">
          <thead className="border-b-2 border-slate-200">
            <tr className="[&>th]:p-2 bg-slate-100">
              <th className="text-center">No</th>
              {columns.map((column, i) => (
                <th key={column.field} onClick={(e) => handleSort(column.data)}>
                  <div className="cursor-pointer hover:underline">
                    {column.field}
                    {column.data === sortColumn ? (
                      <span>
                        {sortOrder === SORT_ASC ? (
                          <span> 🔽</span>
                        ) : (
                          <span> 🔼</span>
                        )}
                      </span>
                    ) : null}
                  </div>
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
                  className="font-semibold text-center bg-slate-200"
                >
                  Tidak ada data tersedia
                </td>
              </tr>
            ) : (
              data.map((data, index) => (
                <tr key={index} className="[&>td]:p-2 hover:bg-slate-200">
                  <td className="text-center">{index + 1}</td>
                  {columns.map((column) => (
                    <td key={column.field}>{data[column.data]}</td>
                  ))}
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

import InputLabel from "@/Components/InputLabel";
import TextInput from "@/Components/TextInput";
import { Head, Link, router, useForm, usePage } from "@inertiajs/react";
import { pickBy } from "lodash";
import { useRef, useState } from "react";

export default function Table({ headers, paginates }) {
  const perpage = useRef(branches.per_page);
  const { url } = usePage();
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState("");

  const handleChangePerpage = (e) => {
    perpage.current = e.target.value;
    getData();
  };

  const handleSearch = (e) => {
    e.preventDefault();
    getData();
  };

  const getData = () => {
    setLoading(true);
    router.get(
      route().current(),
      pickBy({
        perpage: perpage.current,
        search,
      }),
      {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => setLoading(false),
      }
    );
  };

  const TableHeader = ({ headers, className }) => (
    <thead className="border border-gray-200 rounded-lg">
      <tr className="[&>th]:p-2 bg-slate-100">
        {headers.map((header, index) => (
          <th key={index} className={className}>
            {header}
          </th>
        ))}
      </tr>
    </thead>
  );

  const TableBody = ({ data, className }) => (
    <tbody className={className}>
      {data.map((row, index) => (
        <tr key={index} className="[&>td]:p-2">
          {row.map((cell, index) => (
            <td key={index}>{cell}</td>
          ))}
        </tr>
      ))}
    </tbody>
  )
  return (
    <>
      <div className="flex items-center justify-between mb-4">
        <select
          name="perpage"
          id="perpage"
          className="rounded-lg bg-slate-100"
          value={perpage.current}
          onChange={handleChangePerpage}
        >
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        <div>
          <form onSubmit={handleSearch}>
            <div className="flex items-center gap-2">
              <InputLabel htmlFor="search">Search : </InputLabel>
              <TextInput
                type="search"
                name="search"
                value={search}
                onChange={(e) => setSearch(e.target.value)}
              />
              <PrimaryButton type="submit">Cari</PrimaryButton>
            </div>
          </form>
        </div>
      </div>

      <table className="w-full">
        <thead className="border border-gray-200 rounded-lg">
          <tr className="[&>th]:p-2 bg-slate-100">
            <th className="text-left">No</th>
            <th>Kode Cabang</th>
            <th>Nama Cabang</th>
            <th>Alamat</th>
          </tr>
        </thead>
        <tbody>
          {loading ? (
            <tr>
              <td>Loading ... </td>
            </tr>
          ) : (
            paginates.data.map((data, index) => (
              <tr key={data.id} className="[&>td]:p-2">
                <td>{paginates.from + index}</td>
                <td>{data.branch_code}</td>
                <td>{data.branch_name}</td>
                <td>{data.address}</td>
              </tr>
            ))
          )}
        </tbody>
      </table>
      <div className="flex items-center justify-between mt-4">
        <div>
          Showing {paginates.from} to {paginates.to} total {paginates.total}
        </div>
        <div className="flex items-center gap-2">
          {!paginates.first_page_url.includes(url) && (
            <Link
              href={paginates.first_page_url}
              className="p-2 text-sm rounded-lg bg-slate-100"
              preserveScroll
              preserveState
            >
              <div>First</div>
            </Link>
          )}
          {paginates.links.map(
            (link, index) =>
              link.url && (
                <Link
                  key={index}
                  href={link.url}
                  className={`${
                    link.url.includes(url) ? `bg-slate-200` : `bg-slate-100`
                  } py-2 px-3 text-sm rounded-lg`}
                  preserveScroll
                  preserveState
                >
                  <div dangerouslySetInnerHTML={{ __html: link.label }}></div>
                </Link>
              )
          )}
          {!paginates.last_page_url.includes(url) && (
            <Link
              href={paginates.last_page_url}
              className="p-2 text-sm rounded-lg bg-slate-100"
              preserveScroll
              preserveState
            >
              <div>Last</div>
            </Link>
          )}
        </div>
      </div>
    </>
  );
}

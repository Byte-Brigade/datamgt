import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Link, router, usePage } from "@inertiajs/react";
import { pickBy } from "lodash";
import { useRef, useState } from "react";

export default function Table({ users }) {
  const perpage = useRef(users.per_page);
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
        <thead>
          <tr className="[&>th]:p-2 bg-slate-100">
            <th className="text-left">No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          {loading ? (
            <tr>
              <td>Loading ... </td>
            </tr>
          ) : (
            users.data.map((user, index) => (
              <tr key={user.id} className="[&>td]:p-2">
                <td>{users.from + index}</td>
                <td>{user.name}</td>
                <td>{user.email}</td>
                <td>edit | delete</td>
              </tr>
            ))
          )}
        </tbody>
      </table>
      <div className="flex items-center justify-between mt-4">
        <div>
          Showing {users.from} to {users.to} total {users.total}
        </div>
        <div className="flex items-center gap-2">
          {!users.first_page_url.includes(url) && (
            <Link
              href={users.first_page_url}
              className="bg-slate-100 p-2 text-sm rounded-lg"
              preserveScroll
              preserveState
            >
              <div>First</div>
            </Link>
          )}
          {users.links.map(
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
          {!users.last_page_url.includes(url) && (
            <Link
              href={users.last_page_url}
              className="bg-slate-100 p-2 text-sm rounded-lg"
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

import { Pagination, Search, TableHeader } from "@/Components/DataTable";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import { pickBy } from "lodash";
import { useRef, useState } from "react";

export default function Cabang({ branches, sessions }) {
  const { data, setData, post, processing, errors } = useForm({
    file: null,
  });
  const perpage = useRef(branches.per_page);
  const { url } = usePage();
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState("");

  const headers = [
    { name: "No", field: "id" },
    { name: "Kode Cabang", field: "branch_code" },
    { name: "Nama Cabang", field: "branch_name" },
    { name: "Alamat", field: "address" },
  ];

  const submit = (e) => {
    e.preventDefault();
    post(route("branches.import"));
  };

  const exportData = (e) => {
    e.preventDefault();

    window.open(route("branches.export"), "__blank");
  };

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

  const [isOpen, setIsOpen] = useState(false);

  const toggleModal = () => {
    setIsOpen(!isOpen);
  };

  console.log(branches);
  return (
    <AuthenticatedLayout>
      <Head title="Cabang" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>
            {sessions.status && (
              <p className="font-semibold text-green-400">{sessions.message}</p>
            )}
          </div>
          <div className="flex items-center justify-between mb-4">
            <PrimaryButton
              className="bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
              onClick={toggleModal}
            >
              <div className="flex items-center gap-x-1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="icon icon-tabler icon-tabler-plus"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  strokeWidth="2"
                  stroke="currentColor"
                  fill="none"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M12 5l0 14"></path>
                  <path d="M5 12l14 0"></path>
                </svg>
                Import Excel
              </div>
            </PrimaryButton>
            <PrimaryButton onClick={exportData}>Create Report</PrimaryButton>
          </div>
          <Search
            perpage={perpage}
            handleSearch={handleSearch}
            handleChangePerpage={handleChangePerpage}
            search={search}
            setSearch={setSearch}
          />
          <div className="relative overflow-x-auto border-2 rounded-lg border-slate-200">
            <table className="w-full">
              <TableHeader headers={headers} />
              <tbody>
                {loading ? (
                  <tr>
                    <td
                      className="p-4 text-lg font-semibold text-center transition-colors duration-75 bg-slate-200 animate-pulse"
                      colSpan="4"
                    >
                      Loading ...{" "}
                    </td>
                  </tr>
                ) : (
                  branches.data.map((branch, index) => (
                    <tr key={branch.id} className="[&>td]:p-2">
                      <td>{branches.from + index}</td>
                      <td>{branch.branch_code}</td>
                      <td>{branch.branch_name}</td>
                      <td>{branch.address}</td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
          <Pagination data={branches} url={url} />
        </div>
      </div>
      <Modal show={isOpen}>
        <div className="flex flex-col p-4 gap-y-4">
          <h3 className="text-xl font-semibold text-center">Import Data</h3>
          <form onSubmit={submit} encType="multipart/form-data">
            <div className="flex flex-col">
              <label htmlFor="import">Import Excel (.xlsx)</label>
              <input
                className="bg-gray-100 border-2 border-gray-200 rounded-lg"
                onChange={(e) => setData("file", e.target.files[0])}
                type="file"
                name="import"
                id="import"
                accept=".xlsx"
              />
            </div>
            <div className="flex justify-between mt-4 gap-x-4">
              <SecondaryButton type="button" onClick={toggleModal}>
                Close Modal
              </SecondaryButton>
              <PrimaryButton
                type="submit"
                onClick={toggleModal}
                disabled={processing}
              >
                Import Data
              </PrimaryButton>
            </div>
          </form>
        </div>
      </Modal>
    </AuthenticatedLayout>
  );
}

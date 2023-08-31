import Alert from "@/Components/Alert";
import InputLabel from "@/Components/InputLabel";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import TextInput from "@/Components/TextInput";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, router, useForm, usePage } from "@inertiajs/react";
import { pickBy } from "lodash";
import { useRef, useState } from "react";

export default function SKBIRTGS({ sessions, sks }) {
  const { data, setData, post, processing, errors } = useForm({
    file: null,
  });
  const perpage = useRef(sks.per_page);
  const { url } = usePage();
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState("");
  const [isModalImportOpen, setIsModalImportOpen] = useState(false);
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isModalUploadOpen, setIsModalUploadOpen] = useState(false);

  const submit = (e) => {
    e.preventDefault();
    post(route("ops.skbirtgs.import"));
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

  const toggleModalImport = () => {
    setIsModalImportOpen(!isModalImportOpen);
  };

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  const toggleModalUpload = () => {
    setIsModalUploadOpen(!isModalUploadOpen);
  };


  console.log(sks);
  return (
    <AuthenticatedLayout>
      <Head title="OPS | Surat Kuasa BI RGTS" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex items-center justify-between mb-4">
            <PrimaryButton
              className="bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
              onClick={toggleModalImport}
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
            <PrimaryButton onClick={toggleModalExport}>
              Create Report
            </PrimaryButton>
          </div>
          <div className="flex items-center justify-between mb-4">
            <div className="flex items-center gap-x-2">
              Show
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
              entries
            </div>
            <div>
              <form onSubmit={handleSearch}>
                <div className="flex items-center gap-2">
                  <InputLabel htmlFor="search">Search : </InputLabel>
                  <TextInput
                    type="search"
                    name="search"
                    id="search"
                    value={search}
                    onChange={(e) => setSearch(e.target.value)}
                  />
                  <PrimaryButton type="submit">Cari</PrimaryButton>
                </div>
              </form>
            </div>
          </div>
          <div className="relative overflow-x-auto border-2 rounded-lg border-slate-200">
            <table className="w-full text-sm">
              <thead className="border-b-2 border-slate-200">
                <tr className="[&>th]:p-2 bg-slate-100">
                  <th className="text-left">No</th>
                  <th>Jenis Surat</th>
                  <th>Nomor Surat</th>
                  <th>Kantor Cabang</th>
                  <th>Penerima Kuasa</th>
                  <th>Lampiran</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                {loading ? (
                  <tr>
                    <td
                      className="p-4 text-lg font-semibold text-center transition-colors duration-75 bg-slate-200 animate-pulse"
                      colSpan="7"
                    >
                      Loading ...{" "}
                    </td>
                  </tr>
                ) : (
                  sks.data.map((sk, index) => (
                    <tr key={sk.id} className="[&>td]:p-2">
                      <td>{sks.from + index}</td>
                      <td>Surat Kuasa BI RTGS</td>
                      <td>{sk.no_surat}</td>
                      <td>{sk.branches.branch_name}</td>
                      <td>
                        {sk.penerima_kuasa.length > 0
                          ? sk.penerima_kuasa
                              .map((employee) => employee.name)
                              .join(" - ")
                          : "-"}
                      </td>
                      <td>
                        {sk.file ? (
                          sk.file
                        ) : (
                          <button onClick={toggleModalUpload} className="text-blue-600 hover:underline hover:cursor-pointer">
                            Upload FIle
                          </button>
                        )}
                      </td>
                      <td>{sk.status}</td>
                      <td>Edit | Delete</td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
          <div className="flex items-center justify-between mt-4">
            <div>
              Showing {sks.from} to {sks.to} of {sks.total} entries
            </div>
            <div className="flex items-center gap-2">
              {!sks.first_page_url.includes(url) && (
                <Link
                  href={sks.first_page_url}
                  className="p-2 text-sm rounded-lg bg-slate-100"
                  preserveScroll
                  preserveState
                >
                  <div>First</div>
                </Link>
              )}
              {sks.links.map(
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
                      <div
                        dangerouslySetInnerHTML={{ __html: link.label }}
                      ></div>
                    </Link>
                  )
              )}
              {!sks.last_page_url.includes(url) && (
                <Link
                  href={sks.last_page_url}
                  className="p-2 text-sm rounded-lg bg-slate-100"
                  preserveScroll
                  preserveState
                >
                  <div>Last</div>
                </Link>
              )}
            </div>
          </div>
        </div>
      </div>
      <Modal show={isModalImportOpen}>
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
              <SecondaryButton type="button" onClick={toggleModalImport}>
                Close Modal
              </SecondaryButton>
              <PrimaryButton
                type="submit"
                onClick={toggleModalImport}
                disabled={processing}
              >
                Import Data
              </PrimaryButton>
            </div>
          </form>
        </div>
      </Modal>
      <Modal show={isModalUploadOpen}>
        <div className="flex flex-col p-4 gap-y-4">
          <h3 className="text-xl font-semibold text-center">Upload Data Lampiran</h3>
          <form onSubmit={submit} encType="multipart/form-data">
            <div className="flex flex-col">
              <label htmlFor="upload">Upload Lampiran (.pdf)</label>
              <input
                className="bg-gray-100 border-2 border-gray-200 rounded-lg"
                onChange={(e) => setData("file", e.target.files[0])}
                type="file"
                name="upload"
                id="upload"
                accept=".pdf"
              />
            </div>
            <div className="flex justify-between mt-4 gap-x-4">
              <SecondaryButton type="button" onClick={toggleModalUpload}>
                Close Modal
              </SecondaryButton>
              <PrimaryButton
                type="submit"
                onClick={toggleModalUpload}
                disabled={processing}
              >
                Upload Data
              </PrimaryButton>
            </div>
          </form>
        </div>
      </Modal>
    </AuthenticatedLayout>
  );
}

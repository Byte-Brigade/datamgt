import Alert from "@/Components/Alert";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function SKBIRTGS({ sessions }) {
  const { data, setData, post, processing, errors } = useForm({
    file: null,
  });
  const [isModalImportOpen, setIsModalImportOpen] = useState(false);
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isModalUploadOpen, setIsModalUploadOpen] = useState(false);
  const [id, setId] = useState(0);
  const [isRefreshed, setIsRefreshed] = useState(false);
  console.log(isRefreshed)

  const columns = [
    { name: "Jenis Surat", value: "Surat Kuasa BI RTGS" },
    { name: "Nomor Surat", field: "no_surat" },
    { name: "Kantor Cabang", field: "branches.branch_name" },
    {
      name: "Penerima Kuasa",
      field: "penerima_kuasa.name",
      type: "custom",
      render: (data) =>
        data.penerima_kuasa.map((employee) => employee.name).join(" - ") || "-",
    },
    {
      name: "Lampiran",
      field: "file",
      type: "custom",
      render: (data) =>
        data.file || (
          <button
            onClick={() => {
              toggleModalUpload();
              setId(data.id);
            }}
            className="text-blue-500 hover:underline"
          >
            Upload File
          </button>
        ),
    },
    { name: "Status", field: "status" },
    { name: "Action", field: "action", render: () => <DropdownMenu /> },
  ];
  const submit = (e) => {
    e.preventDefault();
    post(route("ops.skbirtgs.import"));
  };

  const uploadLampiran = (e) => {
    e.preventDefault();
    post(route("ops.skbirtgs.upload", id), {
      replace: true,
      onFinish: () => setIsRefreshed(!isRefreshed),
    });
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
          <DataTable
            columns={columns}
            fetchUrl={"/api/ops/skbirtgs"}
            refreshUrl={isRefreshed}
          />
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
          <h3 className="text-xl font-semibold text-center">
            Upload Data Lampiran
          </h3>
          <form onSubmit={uploadLampiran} encType="multipart/form-data">
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
            <p>{id}</p>
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

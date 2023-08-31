import Alert from "@/Components/Alert";
import DataTable from "@/Components/DataTable";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Cabang({ sessions }) {
  const { data, setData, post, processing, errors } = useForm({
    file: null,
  });

  const columns = [
    { name: "Kode Cabang", field: "branch_code" },
    { name: "Nama Cabang", field: "branch_name" },
    { name: "Alamat", field: "address" },
    { name: "Action", field: "action", render: () => <>Action</> },
  ];

  const submit = (e) => {
    e.preventDefault();
    post(route("branches.import"));
  };

  const exportData = (e) => {
    e.preventDefault();

    window.open(route("branches.export"), "__blank");
  };

  const [isOpen, setIsOpen] = useState(false);

  const toggleModal = () => {
    setIsOpen(!isOpen);
  };

  return (
    <AuthenticatedLayout>
      <Head title="Cabang" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
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
          <DataTable columns={columns} fetchUrl={"/api/branches"} />
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

import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentArrowDownIcon } from "@heroicons/react/24/outline";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head, useForm } from "@inertiajs/react";
import {
  Button,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
} from "@material-tailwind/react";
import { useState } from "react";

export default function Detail({ auth, branch, sessions, slug }) {
  const initialData = {
    file: null,
    branch: "0",
    position: "0",
    employee_id: null,
    name: null,
    email: null,
    branches: {
      id: null,
    },
    employee_positions: {
      id: null,
    },
    gender: null,
    birth_date: null,
    hiring_date: null,
  };
  const {
    data,
    setData,
    post,
    put,
    delete: destroy,
    processing,
    errors,
  } = useForm(initialData);
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);

  const columns = [
    { name: "Nama Cabang", field: "branches.branch_name", sortable: true },
    {
      name: "Posisi",
      field: "employee_positions.position_name",
      sortable: true,
      filterable: true,
    },
    { name: "NIK", field: "employee_id", sortable: true },
    {
      name: "Nama Lengkap",
      field: "name",
      sortable: true,
      className: "w-[300px]",
    },
    { name: "Email (@banksampoerna.com)", field: "email" },
    {
      name: "Tanggal Lahir",
      field: "birth_date",
      type: "date",
      className: "text-center w-[300px]",
    },
    {
      name: "Join Date",
      field: "hiring_date",
      type: "date",
      className: "text-center w-[300px]",
    },
  ];

  const handleSubmitExport = (e) => {
    e.preventDefault();
    const { branch, position } = data;
    const query =
      branch !== "0" && position !== "0"
        ? `?branch=${branch}&position=${position}`
        : branch !== "0"
        ? `?branch=${branch}`
        : position !== "0"
        ? `?position=${position}`
        : "";

    window.open(route("ops.employees.export") + query, "__blank");
  };

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <BreadcrumbsDefault />
      <Head title="Karyawan Bank OPS Cabang" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex justify-end">
            <h2 className="text-xl font-semibold text-end">
              {branch.branch_name}
            </h2>
          </div>
          {hasRoles("superadmin|admin|branch_ops", auth) &&
            ["can add", "can export"].some((permission) =>
              auth.permissions.includes(permission)
            ) && (
              <div className="flex items-center justify-between mb-4">
                {auth.permissions.includes("can export") && (
                  <PrimaryButton onClick={toggleModalExport}>
                    <div className="flex items-center gap-x-2">
                      <DocumentArrowDownIcon className="w-4 h-4" />
                      Create Report
                    </div>
                  </PrimaryButton>
                )}
              </div>
            )}
          <DataTable
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("superadmin|admin|branch_ops", auth) &&
                  ["can edit", "can delete"].some((permission) =>
                    auth.permissions.includes(permission)
                  )
                : true
            )}
            fetchUrl={`/api/inquery/staff/${slug}`}
            refreshUrl={isRefreshed}
          />
        </div>
      </div>
      {/* Modal Export */}
      <Dialog open={isModalExportOpen} handler={toggleModalExport} size="md">
        <DialogHeader className="flex items-center justify-between">
          Export Data
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={toggleModalExport}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <form onSubmit={handleSubmitExport} encType="multipart/form-data">
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">Export</div>
          </DialogBody>
          <DialogFooter>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={processing} type="submit">
                Simpan
              </Button>
              <SecondaryButton type="button" onClick={toggleModalExport}>
                Tutup
              </SecondaryButton>
            </div>
          </DialogFooter>
        </form>
      </Dialog>
    </AuthenticatedLayout>
  );
}

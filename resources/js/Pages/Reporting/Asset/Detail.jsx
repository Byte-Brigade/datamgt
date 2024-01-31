import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head, Link, useForm, usePage } from "@inertiajs/react";
import {
  Button,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
  Typography
} from "@material-tailwind/react";
import { useState } from "react";

export default function Branch({ auth, sessions, type_name }) {
  const { url } = usePage();
  const initialData = {
    file: null,
    branch_code: null,
    branch_name: null,
    address: null,
    branch_type_id: null,
    layanan_atm: null,
    npwp: null,
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

  const headings = [
    {
      name: 'Tipe',
      colSpan: 2,
    },
    {
      name: 'Category',
      colSpan: 2,
    },
  ]
  const columns = [

    {
      name: "Nama", field: "branch_name", sortable: false,
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("reporting.assets.detail.branch", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Depre", field: "depre", sortable: false,

    },
    {
      name: "Non-Depre", field: "non_depre", sortable: false,

    },

  ];


  const handleSubmitExport = (e) => {
    e.preventDefault();
    setIsModalExportOpen(!isModalExportOpen);
    window.open(route("reporting.bros.export"), "_self");
  };


  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };


  return (
    <AuthenticatedLayout auth={auth}>
      <BreadcrumbsDefault url={url} />
      <Head title="Data Report Asset" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <h2 className="text-xl text-end font-semibold">
         {type_name}
            </h2>
          <div className="flex items-center justify-between mb-4">
            <PrimaryButton onClick={toggleModalExport}>
              Create Report
            </PrimaryButton>
          </div>
          <DataTable
            headings={headings}
            columns={columns}
            fetchUrl={`/api/report/assets/${type_name}`}
            refreshUrl={isRefreshed}
            bordered={true}
          />
        </div>
      </div>

      {/* Modal Export */}
      <Dialog open={isModalExportOpen} handler={toggleModalExport} size="md">
        <DialogHeader className="flex items-center justify-between">
          Create Report
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
        <DialogBody divider>
          <div className="flex flex-col gap-y-4">
            <Typography>Export data</Typography>
          </div>
        </DialogBody>
        <DialogFooter>
          <div className="flex flex-row-reverse gap-x-4">
            <Button
              onClick={handleSubmitExport}
              disabled={processing}
              type="submit"
            >
              Buat
            </Button>
            <SecondaryButton type="button" onClick={toggleModalExport}>
              Tutup
            </SecondaryButton>
          </div>
        </DialogFooter>
      </Dialog>

    </AuthenticatedLayout>
  );
}

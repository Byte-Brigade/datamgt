import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head, useForm, usePage } from "@inertiajs/react";
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

export default function Branch({ auth, sessions }) {
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
      name: 'Jenis Lisensi',
      colSpan: 4,
    },
  ]
  const columns = [

    {
      name: "Nama", field: "type_name", sortable: false,

    },
    {
      name: "Disnaker", field: "disnaker", sortable: false,

    },
    {
      name: "Pajak Reklame", field: "pajak_reklame", sortable: false,

    },
    {
      name: "SKBIRTGS", field: "skbirtgs", sortable: false,

    },
    {
      name: "SK Operasional", field: "skoperational", sortable: false,

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
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex items-center justify-between mb-4">
            <PrimaryButton onClick={toggleModalExport}>
              Create Report
            </PrimaryButton>
          </div>
          <DataTable
            headings={headings}
            columns={columns}
            fetchUrl={"/api/report/licenses"}
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

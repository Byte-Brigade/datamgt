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

export default function Branch({ auth, sessions, branch_types, branches }) {
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

  const [isModalImportOpen, setIsModalImportOpen] = useState(false);
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);
  const columns = [
    { name: "Kode Cabang", field: "branch_code", sortable: false },
    { name: "Nama Cabang", field: "branch_name", sortable: false },
    {
      name: "Tipe Cabang",
      field: "type_name",
      sortable: false,
      filterable: true,
    },
    { name: "Status Kepemilikan", field: "status" },
    { name: "Masa Sewa", field: "masa_sewa" },
    { name: "Jatuh Tempo Sewa", field: "expired_date", type: "date" },
    { name: "Open Date Cabang", field: "open_date", type: "date" },
    { name: "Owner/Pemilik Gedung", field: "owner" },
    { name: "Nilai Pembelian", field: "nilai_pembelian" },
    { name: "Jumlah KDO", field: "kdo_mobil", className: "text-center" },
    { name: "Izin OJK", field: "izin" },
    { name: "Jumlah Karyawan", field: "jumlah_karyawan" },
    {
      name: "Izin Disnaker",
      field: "detail",
      render: (data) => (
        <Link href={route("reporting.disnaker", data.slug)}>
          <Button variant="outlined">Detail</Button>
        </Link>
      ),
    },
  ];

  const handleSubmitExport = (e) => {
    e.preventDefault();
    setIsModalExportOpen(!isModalExportOpen);
    window.open(route("reporting.branches.export"), "_self");
  };

  const toggleModalImport = () => {
    setIsModalImportOpen(!isModalImportOpen);
  };

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <BreadcrumbsDefault url={url} />
      <Head title="Data Cabang" />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex items-center justify-between mb-4">
            <PrimaryButton onClick={toggleModalExport}>
              Create Report
            </PrimaryButton>
          </div>
          <DataTable
            columns={columns}
            fetchUrl={"/api/report/branches"}
            refreshUrl={isRefreshed}
            className="w-[1800px]"
            component={[
              {
                data: Array.from(
                  new Set(branches.map((branch) => branch.layanan_atm))
                ),
                field: "layanan_atm",
              },
              {
                data: Array.from(
                  new Set(
                    branch_types
                      .filter((type) =>
                        ["KC", "KCP", "KF"].includes(type.type_name)
                      )
                      .map((type) => type.type_name)
                  )
                ),
                field: "branch_types.type_name",
              },
            ]}
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
            <Typography>Buat Report Data Cabang?</Typography>
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
            <SecondaryButton type="button" onClick={toggleModalImport}>
              Tutup
            </SecondaryButton>
          </div>
        </DialogFooter>
      </Dialog>
    </AuthenticatedLayout>
  );
}

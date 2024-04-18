import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Detail({ auth, sessions, type, type_item, periode, slug }) {
  const initialData = {
    jumlah_kendaraan: null,
    jumlah_driver: null,
    sewa_kendaraan: null,
    biaya_driver: null,
    ot: null,
    rfid: null,
    non_rfid: null,
    grab: null,
    periode: null,
  };

  console.log(periode)
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
  const [isModalCreateOpen, setIsModalCreateOpen] = useState(false);
  const [isModalEditOpen, setIsModalEditOpen] = useState(false);
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);

  const columns = [
    {
      name: "Jenis Pekerjaan",
      field: "jenis_pekerjaan",
    },
    {
      name: "Nama Pegawai",
      field: "nama_pegawai",
    },
    {
      name: "User",
      field: "user",
    },
    {
      name: "Lokasi",
      field: "lokasi",
    },
    {
      name: "Vendor",
      field: "vendor",
    },
    {
      name: "Cost",
      field: "cost",
      className: "text-center",
      type: "custom",
      render: (data) => data.cost.toLocaleString("id-ID"),
    },
    {
      name: "Periode",
      field: "periode",
      type: "date",
    }
    // {
    //   name: "Detail",
    //   field: "detail",
    //   className: "text-center",
    //   render: (data) => (
    //     <Link href={route("gap.alihdayas.detail", data.divisi_pembebanan)}>
    //       <Button variant="outlined">Detail</Button>
    //     </Link>
    //   ),
    // },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | Alih Daya" />
      <BreadcrumbsDefault />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>

          <DataTable
            columns={columns.filter((column) => column.field === "cost" ? auth.permissions.includes('can alih daya') : true)}
            fetchUrl={`/api/inquery/alihdayas/branch/${slug}/detail?type=${type}&type_item=${type_item}`}
            refreshUrl={isRefreshed}
            parameters={periode}
          />
        </div>
      </div>

    </AuthenticatedLayout>
  );
}

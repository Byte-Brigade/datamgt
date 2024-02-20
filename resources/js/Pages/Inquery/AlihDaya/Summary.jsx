import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import { useFormContext } from "@/Components/Context/FormProvider";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Summary({ auth, sessions }) {
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
  const {
    data,
    setData,
    post,
    put,
    delete: destroy,
    processing,
    errors,
  } = useForm(initialData);
  const { periode } = useFormContext();
  const [isRefreshed, setIsRefreshed] = useState(false);


  const columns = [
    {
      name: "Nama Cabang",
      field: "branch_name",
      type: "custom",
      render: (data) => (
        <Link
          href={route('inquery.alihdayas', data.slug
          )}
        >
          {data.branch_name}
        </Link>
      ),
    },

    {
      name: "Jumlah Tenaga Kerja",
      field: "tenaga_kerja",
    }
    {
      name: "Jumlah Biaya",
      field: "biaya",
      type: "custom",
      className: "text-right",
      render: (data) => data.biaya.toLocaleString("id-ID")
    }

  ];


  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery | Alih Daya" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <DataTable
            columns={columns}
            fetchUrl={"/api/inquery/alihdayas"}
            refreshUrl={isRefreshed}
            bordered={true}
            parameters={{
              branch_id: auth.user.branch_id
            }}
          />
        </div>
      </div>

    </AuthenticatedLayout>
  );
}

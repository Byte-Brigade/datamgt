import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";

export default function Page({ sessions, auth }) {

  const columns = [
    {
      name: "Nama",
      field: "branch_name",
      // className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link className="p-4 cursor-pointer hover:text-blue-500" href={route("inquery.staff.detail", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Tipe Cabang",
      field: "type_name",
      className: 'text-center'
    },
    {
      name: "Jumlah Karyawan",
      field: "jumlah_karyawan",
      className: 'text-center'
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Staff" />
      <BreadcrumbsDefault />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <DataTable
            fetchUrl={"/api/inquery/staff"}
            columns={columns}
            bordered={true}
            parameters={{branch_id: auth.user.branch_id}}
          />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

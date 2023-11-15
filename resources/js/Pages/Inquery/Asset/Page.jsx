import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, usePage } from "@inertiajs/react";

export default function Page({ sessions, auth }) {
  const { url } = usePage();
  const columns = [
    {
      name: "Nama",
      field: "branch_code",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.assets.detail", data.branch_code)}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Tipe",
      field: "branch_types.type_name",
      className: "w-28 text-center",
    },
    {
      name: 'Jumlah Depre',
      field: 'jumlah_depre',
      className: "text-center",
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Depre').length
      }
    },
    {
      name: 'Jumlah Non-Depre',
      field: 'jumlah_non_depre',
      className: 'text-center',
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Non-Depre').length
      }
    },
    {
      name: 'Total',
      field: 'total_assets',
      className: 'text-center',
      type: "custom",
      render: (data) => {
        return data.assets.length
      }
    }
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Branch" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <DataTable
            fetchUrl={"/api/branches"}
            columns={columns}
          />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

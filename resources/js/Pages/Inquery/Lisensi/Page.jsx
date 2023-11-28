import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, usePage } from "@inertiajs/react";

export default function Page({ sessions, auth }) {

  const columns = [
    {
      name: "Nama",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
    },

    {
      name: 'Izin/Ojk',
      field: 'izin',
      className: "text-center",
    },

    {
      name: 'SK BI RTGS',
      field: 'skbirtgs',
      className: "text-center",
    },
    {
      name: 'SK Operasional',
      field: 'skoperasional',
      className: "text-center",
    },
    {
      name: 'Reklame',
      field: 'pajak_reklame',
      className: "text-center",
    },
    {
      name: 'Apar',
      field: 'apar',
      className: "text-center",
    },
    {
      name: 'Disnaker',
      field: 'disnaker',
      className: "text-center",
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <DataTable
            fetchUrl={"/api/inquery/licenses"}
            columns={columns}
            bordered={true}
          />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

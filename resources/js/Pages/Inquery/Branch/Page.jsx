import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, usePage } from "@inertiajs/react";

export default function Branch({ sessions, auth }) {
  const { url } = usePage();
  const columns = [
    {
      name: "Nama",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.branch.detail", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Tipe",
      field: "type_name",
      className: "w-28 text-center",
    },
    { name: "Area", field: "area" },
    { name: "Alamat", field: "address", className: "w-[300px]" },
    { name: "BM", field: "bm" },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Branch" />
      <BreadcrumbsDefault />
      <div className="p-4 border border-gray-200 bg-white rounded-lg dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <DataTable
            fetchUrl={"/api/inquery/branches"}
            columns={columns}
            parameters={{ branch_id: auth.user.branch_id }}
          />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

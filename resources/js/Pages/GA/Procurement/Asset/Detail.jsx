import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

import { useState } from "react";

export default function Detail({ auth, disnaker, sessions }) {
  const [isRefreshed, setIsRefreshed] = useState(false);

  const columns = [
    { name: "Cabang", field: "branches.branch_name" },

    {
      name: "Jenis Perizinan",
      field: "jenis_perizinan.name",
      className: "text-center",
    },
    {
      name: "Tgl Pengesahan",
      field: "tgl_pengesahan",
      className: "text-center",
    },
    {
      name: "Tgl Masa Berlaku s/d",
      field: "tgl_masa_berlaku",
      className: "text-center",
    },
    {
      name: "Progress Resertifikasi",
      field: "progress_resertifikasi",
      className: "text-center",
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA | Izin Disnaker" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <h2 className="mb-4 text-xl font-semibold text-center">
            {disnaker.branches.branch_name}
          </h2>
          <DataTable
            columns={columns}
            fetchUrl={`/api/infra/disnaker/${disnaker.branches.id}/report`}
            refreshUrl={isRefreshed}
          />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

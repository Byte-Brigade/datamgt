import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, usePage } from "@inertiajs/react";

export default function Page({ sessions, auth }) {
  const { url } = usePage();
  const headings = [
    {
      name: 'Lokasi',
      colSpan: 2,
    },
    {
      name: 'Kategori A (Depresiasi)',
      colSpan: 4,
    },
    {
      name: 'Kategori B (Non-Depresiasi)',
      colSpan: 4,
    },

  ]
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
      name: 'Item',
      field: 'item_depre',
      className: "text-center",
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Depre').length
      }
    },
    {
      name: 'Nilai Perolehan',
      field: 'nilai_perolehan_depre',
      className: "text-right",
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Depre').reduce(
          (acc, item) => {
            return acc + item.asset_cost
          },0
        ).toLocaleString('id-ID')
      }
    },
    {
      name: 'Penyusutan',
      field: 'penyusutan',
      className: "text-right",
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Depre').reduce(
          (acc, item) => {
            return acc + item.accum_depre
          },0
        ).toLocaleString('id-ID')
      }
    },
    {
      name: 'Net Book Value',
      field: 'net_book_value',
      className: "text-right",
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Depre').reduce(
          (acc, item) => {
            return acc + item.net_book_value
          },0
        ).toLocaleString('id-ID')
      }
    },
    {
      name: 'Item',
      field: 'item_non_depre',
      className: "text-center",
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Non-Depre').length
      }
    },
    {
      name: 'Nilai Perolehan',
      field: 'nilai_perolehan_non_depre',
      className: "text-center",
      type: "custom",
      render: (data) => {
        return data.assets.filter(asset => asset.category === 'Non-Depre').reduce(
          (acc, item) => {
            return acc + item.asset_cost
          },0
        ).toLocaleString('id-ID')
      }
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <DataTable
            fetchUrl={"/api/inquery/assets"}
            columns={columns}
            headings={headings}
            bordered={true}
          />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

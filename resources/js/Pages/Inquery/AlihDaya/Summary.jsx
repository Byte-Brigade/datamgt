import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import { useFormContext } from "@/Components/Context/FormProvider";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import CardMenu from "@/Pages/Dashboard/Partials/CardMenu";
import { tabState } from "@/Utils/TabState";
import { ArchiveBoxIcon } from "@heroicons/react/24/outline";
import { Head, Link, useForm } from "@inertiajs/react";
import { useState } from "react";

export default function Page({ auth, sessions }) {
  const { datePickerValue } = useFormContext();
  const [isRefreshed, setIsRefreshed] = useState(false);
  const { active, params, handleTabChange } = tabState([
    "tenaga-kerja",
    "biaya",
  ]);

  const heading1 = [
    {
      name: "Jenis Pekerjaan",
      colSpan: 2,
    },
    {
      name: "Jumlah Tenaga Kerja",
      colSpan: 7,
    },
  ];

  const heading2 = [
    {
      name: "Jumlah Biaya Tenaga Kerja",
      colSpan: 9,
    },
  ];
  const columnItems = [
    {
      name: "Nama",
      field: "jenis_pekerjaan",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link
          href={route("inquery.alihdayas.detail.type", {
            type: "jenis_pekerjaan",
            type_item: data.jenis_pekerjaan,
            ...datePickerValue,
          })}
        >
          {data.jenis_pekerjaan}
        </Link>
      ),
    },
    {
      name: "Permata",
      field: "permata",
      type: "custom",
      agg: "count",
      className: "text-center",
      render: (data) =>
        data.vendor.filter((item) => item.vendor === "Permata").length,
    },
    {
      name: "Sigap",
      field: "sigap",
      type: "custom",
      agg: "count",
      className: "text-center",
      render: (data) =>
        data.vendor.filter((item) => item.vendor === "SIGAP").length,
    },
    {
      name: "Pusaka",
      field: "pusaka",
      type: "custom",
      agg: "count",
      className: "text-center",
      render: (data) =>
        data.vendor.filter((item) => item.vendor === "Pusaka").length,
    },
    {
      name: "Assa",
      field: "assa",
      type: "custom",
      agg: "count",
      className: "text-center",
      render: (data) =>
        data.vendor.filter((item) => item.vendor === "Assa").length,
    },
    {
      name: "Indorent",
      field: "indorent",
      type: "custom",
      agg: "count",
      className: "text-center",
      render: (data) =>
        data.vendor.filter((item) => item.vendor === "Indorent").length,
    },
    {
      name: "Salawati",
      field: "salawati",
      type: "custom",
      agg: "count",
      className: "text-center",
      render: (data) =>
        data.vendor.filter((item) => item.vendor === "Salawati").length,
    },
    {
      name: "Total",
      field: "vendor",
      type: "custom",
      agg: "count",
      className: "text-center",
      render: (data) => data.vendor.length,
    },
  ];

  const columnCosts = [
    {
      name: "Jenis Pekerjaan",
      field: "jenis_pekerjaan",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link
          href={route("inquery.alihdayas.detail.type", {
            type: "jenis_pekerjaan",
            type_item: data.jenis_pekerjaan,
            ...datePickerValue,
          })}
        >
          {data.jenis_pekerjaan}
        </Link>
      ),
    },
    {
      name: "Permata",
      field: "vendor",
      type: "custom",
      agg: "sum",
      format: "currency",
      className: "text-right tabular-nums",
      render: (data) =>
        data.vendor
          .filter((item) => item.vendor === "Permata")
          .reduce((total, acc) => {
            return total + acc.cost;
          }, 0)
          .toLocaleString("id-ID"),
    },
    {
      name: "Sigap",
      field: "vendor",
      type: "custom",
      agg: "sum",
      format: "currency",
      className: "text-right tabular-nums",
      render: (data) =>
        data.vendor
          .filter((item) => item.vendor === "SIGAP")
          .reduce((total, acc) => {
            return total + acc.cost;
          }, 0)
          .toLocaleString("id-ID"),
    },
    {
      name: "Pusaka",
      field: "vendor",
      type: "custom",
      agg: "sum",
      format: "currency",
      className: "text-right tabular-nums",
      render: (data) =>
        data.vendor
          .filter((item) => item.vendor === "Pusaka")
          .reduce((total, acc) => {
            return total + acc.cost;
          }, 0)
          .toLocaleString("id-ID"),
    },
    {
      name: "Assa",
      field: "vendor",
      type: "custom",
      agg: "sum",
      format: "currency",
      className: "text-right tabular-nums",
      render: (data) =>
        data.vendor
          .filter((item) => item.vendor === "Assa")
          .reduce((total, acc) => {
            return total + acc.cost;
          }, 0)
          .toLocaleString("id-ID"),
    },
    {
      name: "Indorent",
      field: "vendor",
      type: "custom",
      agg: "sum",
      format: "currency",
      className: "text-right tabular-nums",
      render: (data) =>
        data.vendor
          .filter((item) => item.vendor === "Indorent")
          .reduce((total, acc) => {
            return total + acc.cost;
          }, 0)
          .toLocaleString("id-ID"),
    },
    {
      name: "Salawati",
      field: "vendor",
      type: "custom",
      agg: "sum",
      format: "currency",
      className: "text-right tabular-nums",
      render: (data) =>
        data.vendor
          .filter((item) => item.vendor === "Salawati")
          .reduce((total, acc) => {
            return total + acc.cost;
          }, 0)
          .toLocaleString("id-ID"),
    },
    {
      name: "Total",
      field: "total_biaya",
      type: "custom",
      agg: "sum",
      format: "currency",
      className: "text-right tabular-nums",
      render: (data) => data.total_biaya.toLocaleString("id-ID"),
    },
  ];

  const columnLocations = [
    {
      name: "Nama Cabang",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.alihdayas.branch", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Jumlah Tenaga Kerja",
      field: "tenaga_kerja",
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | Alih Daya" />
      <BreadcrumbsDefault />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="grid grid-cols-4 gap-4 mb-4">
            <CardMenu
              label="Jumlah Tenaga Alih Daya"
              data
              type="tenaga-kerja"
              Icon={ArchiveBoxIcon}
              active={params.value}
              onClick={() => handleTabChange("tenaga-kerja")}
              color="purple"
            />
            <CardMenu
              label="Jumlah Biaya Tenaga Alih Daya"
              data
              type="biaya"
              Icon={ArchiveBoxIcon}
              active={params.value}
              onClick={() => handleTabChange("biaya")}
              color="purple"
            />
            <CardMenu
              label="Alokasi Tenaga Alih Daya"
              data
              type="lokasi"
              Icon={ArchiveBoxIcon}
              active={params.value}
              onClick={() => handleTabChange("lokasi")}
              color="purple"
            />
          </div>

          {active === "tenaga-kerja" && (
            <DataTable
              columns={columnItems}
              headings={heading1}
              fetchUrl={"/api/inquery/alihdayas"}
              refreshUrl={isRefreshed}
              periodic={true}
              datePicker={{
                year: true,
                month: true,
                date: false,
              }}
              bordered={true}
              parameters={{
                type: "tenaga-kerja",
              }}
            />
          )}

          {active === "biaya" && (
            <DataTable
              columns={columnCosts}
              headings={heading2}
              fetchUrl={"/api/inquery/alihdayas"}
              refreshUrl={isRefreshed}
              bordered={true}
              datePicker={{
                year: true,
                month: true,
                date: false,
              }}
              periodic={true}
              parameters={{
                type: "biaya",
              }}
            />
          )}

          {active === "lokasi" && (
            <DataTable
              columns={columnLocations}
              fetchUrl={"/api/inquery/alihdayas/branch"}
              refreshUrl={isRefreshed}
              bordered={true}
              parameters={{
                branch_id: auth.user.branch_id,
              }}
            />
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

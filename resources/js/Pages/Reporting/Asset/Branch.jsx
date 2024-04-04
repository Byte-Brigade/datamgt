import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import { useFormContext } from "@/Components/Context/FormProvider";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import CardMenu from "@/Pages/Dashboard/Partials/CardMenu";
import { tabState } from "@/Utils/TabState";
import { ArchiveBoxIcon } from "@heroicons/react/24/outline";
import { Head } from "@inertiajs/react";

export default function Branch({ auth, branch, sessions }) {
  const { form, selected, setSelected } = useFormContext();

  const { params, active, handleTabChange } = tabState(["depre", "nonDepre"]);



  const columns = [
    {
      name: "Asset Number",
      field: "asset_number",
      className: "text-center",
      sortable: true,
    },
    {
      name: "Asset Description",
      field: "asset_description",
    },
    {
      name: "Date In Place Service",
      field: "date_in_place_service",
      type: "date",
      sortable: true,
    },
    {
      name: "Assst Cost",
      field: "asset_cost",
      className: "text-right",
      type: "custom",
      sortable: true,
      render: (data) => {
        return data.asset_cost ? data.asset_cost.toLocaleString("id-ID") : "-";
      },
    },
    {
      name: "Depre Exp",
      field: "depre_exp",
      className: "text-right",
      sortable: true,
      type: "custom",
      render: (data) => {
        return data.depre_exp ? data.depre_exp.toLocaleString("id-ID") : "-";
      },
    },
    {
      name: "Accum Depre",
      field: "accum_depre",
      className: "text-right",
      type: "custom",
      sortable: true,
      render: (data) => {
        return data.accum_depre
          ? data.accum_depre.toLocaleString("id-ID")
          : "-";
      },
    },
    {
      name: "Net Book Value",
      field: "net_book_value",
      className: "text-right",
      sortable: true,
      type: "custom",
      render: (data) =>
        data.net_book_value ? data.net_book_value.toLocaleString("id-ID") : "-",
    },
    {
      name: "Asset Location",
      field: "asset_location",
      className: "text-center",
    },
    {
      name: "Major Category",
      field: "major_category",
      className: "text-center",
    },
    {
      name: "Minor Category",
      field: "minor_category",
      className: "text-center",
    },
    {
      name: "Category",
      field: "category",
      className: "text-center",
      sortable: true,
    },
    {
      name: "Ada/Tidak",
      field: "remark",
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Report | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex justify-end">
            <h2 className="text-xl font-semibold text-end">{branch.branch_name}</h2>
          </div>
          <div className="flex justify-between">
            <div className="grid grid-cols-4 gap-4 mb-2">
              <CardMenu
                label="Depre"
                data
                type="depre"
                Icon={ArchiveBoxIcon}
                active
                onClick={() => handleTabChange("depre")}
                color="purple"
              />
              <CardMenu
                label="Non-Depre"
                data
                type="nonDepre"
                Icon={ArchiveBoxIcon}
                active
                onClick={() => handleTabChange("nonDepre")}
                color="purple"
              />
            </div>
          </div>
          {active == "depre" && (
            <DataTable
              columns={columns}
              fetchUrl={`/api/gap/assets`}
              bordered={true}
              parameters={{
                branch_code: branch.branch_code,
                category: "Depre",
              }}
            />)}

          {active == "nonDepre" && (
            <DataTable
              columns={columns}
              fetchUrl={`/api/gap/assets`}
              bordered={true}
              parameters={{
                branch_code: branch.branch_code,
                category: "Non-Depre",
              }}
            />
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

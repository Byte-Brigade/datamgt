import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import { useFormContext } from "@/Components/Context/FormProvider";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import CardMenu from "@/Pages/Dashboard/Partials/CardMenu";
import { tabState } from "@/Utils/TabState";
import { ArchiveBoxIcon } from "@heroicons/react/24/outline";
import { Head } from "@inertiajs/react";
import { Button, Input, Option, Select } from "@material-tailwind/react";
import { useState } from "react";

export default function Detail({ auth, branch, sessions }) {
  const { form } = useFormContext();
  const [selected, setSelected] = useState({});
  const [input, setInput] = useState({});

  const { params, active, handleTabChange } = tabState(["depre", "nonDepre"]);

  const postData = (params) => {
    axios
      .post(
        route("inquery.sto.remark", {
          slug: branch.slug,
        }),
        params
      )
      .then((response) => console.log(response.data.message))
      .catch((err) => console.log(err));
  };

  const handleChanged = (id, value) => {
    setSelected((prevSelected) => {
      const updatedSelected = { ...prevSelected, [id]: value };
      // console.log("Updated Selected:", value); // Add this line for debugging
      // console.log("Updated Selected:", selected); // Add this line for debugging
      postData({
        id,
        remark: value,
      });

      return updatedSelected;
    });

    form.setData("remark", { ...selected, [id]: value });
  };

  const handleInputChange = (id, value) => {
    setInput((prevInput) => {
      const updateInput = { ...prevInput, [id]: value };
      // console.log("Updated Selected:", value); // Add this line for debugging
      // console.log("Updated Selected:", input); // Add this line for debugging
      return updateInput;
    });

    form.setData("keterangan", { ...input, [id]: value });
  };

  const handleBlur = (id, value) => {
    if (value !== "") {
      postData({
        id,
        keterangan: value,
      });
    }
  };

  const handleKeyDown = (id, e) => {
    if (e.key === "Enter" || e.key === "Escape") {
      postData({
        id,
        keterangan: e.target.value,
      });
      e.target.blur();
    }
  };

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
      type: "custom",
      render: (data) =>
        auth.permissions.includes("can sto") ? (
          <Select
            className="bg-white z-50"
            label="Status"
            value={`${data.status || ""}`}
            onChange={(e) => handleChanged(data.id, e)}
          >
            <Option value={`Ada`}>Ada</Option>
            <Option value={`Ada Rusak`}>Ada Rusak</Option>
            <Option value={`Tidak Ada`}>Tidak Ada</Option>
            <Option value={`Lelang`}>Lelang</Option>
            <Option value={`Mutasi`}>Mutasi</Option>
            <Option value={`Non Asset`}>Non Asset</Option>
            <Option value={`Sudah dihapus buku`}>Sudah dihapus buku</Option>
          </Select>
        ) : (
          data.status
        ),
    },
    {
      name: "Keterangan",
      field: "keterangan",
      type: "custom",
      render: (data) =>
        auth.permissions.includes("can sto") ? (
          <Input
            label={"Keterangan"}
            disabled={
              ["Ada", "Ada Rusak"].includes(selected[data.id]) ||
              ["Ada", "Ada Rusak"].includes(data.status)
                ? true
                : false
            }
            value={`${input[data.id] ? input[data.id] : data.keterangan || ""}`}
            onChange={(e) => handleInputChange(data.id, e.target.value)}
            onBlur={(e) => handleBlur(data.id, e.target.value)}
            onKeyDown={(e) => handleKeyDown(data.id, e)}
          />
        ) : (
          data.keterangan
        ),
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex justify-end">
            <h2 className="text-xl font-semibold text-end">
              {branch.branch_name}
            </h2>
          </div>
          <div className="flex justify-between">
            <div className="grid grid-cols-4 gap-4 mb-2">
              <CardMenu
                label="Depre"
                data
                type="depre"
                Icon={ArchiveBoxIcon}
                active={params.value}
                onClick={() => handleTabChange("depre")}
                color="purple"
              />
              <CardMenu
                label="Non-Depre"
                data
                type="nonDepre"
                Icon={ArchiveBoxIcon}
                active={params.value}
                onClick={() => handleTabChange("nonDepre")}
                color="purple"
              />
            </div>
          </div>
          {active == "depre" && (
            <DataTable
              columns={columns}
              fetchUrl={`/api/inquery/stos/detail`}
              bordered={true}
              submitUrl={{ url: `inquery.assets.remark`, id: branch.slug }}
              parameters={{
                branch_code: branch.branch_code,
                category: "Depre",
              }}
            >
              {auth.permissions.includes("can sto") && (
                <Button
                  size="sm"
                  type="submit"
                  className="inline-flex mr-2 bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
                >
                  Submit
                </Button>
              )}
            </DataTable>
          )}

          {active == "nonDepre" && (
            <DataTable
              columns={columns}
              fetchUrl={`/api/inquery/stos/detail`}
              bordered={true}
              submitUrl={{ url: `inquery.assets.remark`, id: branch.slug }}
              parameters={{
                branch_code: branch.branch_code,
                category: "Non-Depre",
              }}
            >
              {auth.permissions.includes("can sto") && (
                <Button
                  size="sm"
                  type="submit"
                  className="inline-flex mr-2 bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
                >
                  Submit
                </Button>
              )}
            </DataTable>
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

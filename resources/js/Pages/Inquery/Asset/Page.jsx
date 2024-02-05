import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import { useFormContext } from "@/Components/Context/FormProvider";
import DataTable from "@/Components/DataTable";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import CardMenu from "@/Pages/Dashboard/Partials/CardMenu";
import { tabState } from "@/Utils/TabState";
import { ArchiveBoxIcon, XMarkIcon } from "@heroicons/react/24/outline";
import { Head, Link } from "@inertiajs/react";
import {
  Button,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
  Input,
  Typography,
} from "@material-tailwind/react";

export default function Page({ sessions, auth, data, type_names }) {
  const { active, params, handleTabChange } = tabState([
    "assets",
    "toner",
    "kdo",
    "sto",
  ]);

  const {
    handleFormSubmit,
    setInitialData,
    isRefreshed,
    setUrl,
    setId,
    modalOpen,
    setModalOpen,
    form,
  } = useFormContext();

  const toggleModalCreate = (id) => {
    setInitialData({ disclaimer: null, branch_code: null });
    setUrl("gap.stos.store");
    form.setData("branch_code", id);

    setModalOpen((prevModalOpen) => {
      const updatedModalOpen = {
        ...prevModalOpen,
        ["create"]: !modalOpen.create,
      };
      return updatedModalOpen;
    });
  };

  const groupBy = (array, key) =>
    array.reduce((result, item) => {
      // Extract the value for the current key
      const keyValue = item[key];

      // If the key doesn't exist in the result object, create it with an empty array
      if (!result[keyValue]) {
        result[keyValue] = [];
      }

      // Push the current item to the array associated with the key
      result[keyValue].push(item);

      return result;
    }, {});

  const headings = [
    {
      name: "Lokasi",
      colSpan: 3,
    },
    {
      name: "Kategori A (Depresiasi)",
      colSpan: 4,
    },
    {
      name: "Kategori B (Non-Depresiasi)",
      colSpan: 4,
    },
  ];

  const columns = [
    {
      name: "Nama",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.assets.detail", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Tipe Cabang",
      field: "type_name",
      filterable: true
    },
    {
      name: "Item",
      field: "item.depre",
      className: "text-center",
    },
    {
      name: "Nilai Perolehan",
      field: "nilai_perolehan.depre",
      className: "text-right",
      agg: "sum",
      format: "currency",
      type: "custom",
      render: (data) => {
        return data.nilai_perolehan.depre.toLocaleString("id-ID");
      },
    },
    {
      name: "Penyusutan",
      field: "penyusutan.depre",
      className: "text-right",
      format: "currency",
      type: "custom",
      render: (data) => {
        return data.penyusutan.depre.toLocaleString("id-ID");
      },
    },
    {
      name: "Net Book Value",
      field: "net_book_value.depre",
      className: "text-right",
      format: "currency",
      type: "custom",
      render: (data) => {
        return data.net_book_value.depre.toLocaleString("id-ID");
      },
    },
    {
      name: "Item",
      field: "item.non_depre",
      className: "text-center",

    },
    {
      name: "Nilai Perolehan",
      field: "nilai_perolehan_non_depre",
      className: "text-right",
      agg: "sum",
      format: "currency",
      type: "custom",
      render: (data) => {
        return data.nilai_perolehan.non_depre.toLocaleString("id-ID");
      },
    },
  ];
  const columnsKdo = [
    { name: "Cabang", field: "branches.branch_name" },
    {
      name: "Jumlah",
      field: "jumlah_kendaraan",
      className: "text-center",
      agg: "sum",
    },
    {
      name: "Tipe Cabang",
      field: "branch_types.type_name",
    },
    {
      name: "Sewa Perbulan",
      field: "sewa_perbulan",
      agg: "sum",
      type: "custom",
      format: "currency",
      render: (data) => data.sewa_perbulan.toLocaleString("id-ID"),
      className: "text-right",
    },
    {
      name: "Jatuh Tempo",
      field: "akhir_sewa",
      type: "date",
      sortable: true,
      className: "justify-center text-center",
    },

    {
      name: "Detail KDO",
      field: "detail",
      className: "text-center",
      render: (data) => (
        <Link href={route("gap.kdos.mobil", data.branches.slug)}>
          <Button variant="outlined">Detail</Button>
        </Link>
      ),
    },
  ];
  const columnsSTO = [
    {
      name: "Cabang",
      field: "branch_code",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.assets.detail", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Tipe Cabang",
      field: "type_name",
      className: "text-center",
    },
    {
      name: "Depre",
      field: "depre",
      className: "text-center",
    },
    {
      name: "Non-Depre",
      field: "non_depre",
      className: "text-center",
    },
    {
      name: "Total Remark",
      field: "total_remarked",
      className: "text-center",
    },
    {
      name: "Sudah STO",
      field: "remarked",
      className: "text-center",
      type: "custom",
      render: (data) => (data.remarked === 1 ? "Sudah" : "Belum"),
    },

    {
      name: "Submit",
      field: "detail",
      className: "text-center",
      render: (data) =>
        data.disclaimer ? (
          <a
            className="text-blue-500 hover:underline text-ellipsis"
            href={`/storage/gap/stos/${data.slug}/${data.disclaimer}`}
            target="__blank"
          >
            {" "}
            {data.disclaimer}
          </a>
        ) : (
          <Button
            onClick={(e) => toggleModalCreate(data.branch_code)}
            variant="outlined"
          >
            Submit
          </Button>
        ),
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div className="grid grid-cols-4 gap-4 mb-4">
            <CardMenu
              label="Asset"
              data
              type="assets"
              Icon={ArchiveBoxIcon}
              active={params.value}
              onClick={() => handleTabChange("assets")}
              color="purple"
            />
            <CardMenu
              label="Toner"
              data
              type="toner"
              Icon={ArchiveBoxIcon}
              active={params.value}
              onClick={() => handleTabChange("toner")}
              color="purple"
            />
            <CardMenu
              label="KDO"
              data
              type="kdo"
              Icon={ArchiveBoxIcon}
              active={params.value}
              onClick={() => handleTabChange("kdo")}
              color="purple"
            />
            <CardMenu
              label="STO"
              data
              type="sto"
              Icon={ArchiveBoxIcon}
              active={params.value}
              onClick={() => handleTabChange("sto")}
              color="purple"
            />
          </div>

          {active === "assets" && (
            <DataTable
              fetchUrl={"/api/inquery/assets"}
              columns={columns}
              parameters={{ branch_id: auth.user.branch_id }}
              headings={headings}
              bordered={true}
              component={[
                {
                  data: type_names,
                  field: 'type_name'
                }
              ]}
            />
          )}
          {active === "sto" && (
            <>
              <DataTable
                fetchUrl={"/api/inquery/stos"}
                columns={columnsSTO}
                parameters={{ branch_id: auth.user.branch_id }}
                isRefreshed={isRefreshed}
                bordered={true}
              />
              <Dialog
                open={modalOpen.create}
                handler={toggleModalCreate}
                size="md"
              >
                <DialogHeader className="flex items-center justify-between">
                  Disclaimer
                  <IconButton
                    size="sm"
                    variant="text"
                    className="p-2"
                    color="gray"
                    onClick={toggleModalCreate}
                  >
                    <XMarkIcon className="w-6 h-6" />
                  </IconButton>
                </DialogHeader>
                <form onSubmit={handleFormSubmit}>
                  <DialogBody divider>
                    <div className="flex flex-col gap-y-4">
                      <Typography>
                        BSM dan BSO menyatakan sudah melakukan STO dengan ini
                        bertanggung jawab...
                      </Typography>

                      <Input
                        variant="standard"
                        label="Upload Lampiran (.pdf)"
                        type="file"
                        name="upload"
                        id="upload"
                        accept=".pdf"
                        onChange={(e) =>
                          form.setData("file", e.target.files[0])
                        }
                      />
                    </div>
                  </DialogBody>
                  <DialogFooter>
                    <div className="flex flex-row-reverse gap-x-4">
                      <Button disabled={form.processing} type="submit">
                        Ubah
                      </Button>
                      <SecondaryButton
                        type="button"
                        onClick={toggleModalCreate}
                      >
                        Tutup
                      </SecondaryButton>
                    </div>
                  </DialogFooter>
                </form>
              </Dialog>
            </>
          )}

          {/* Toner */}
          {active === "toner" && (
            <div className="mt-4">
              <div className="overflow-x-auto">
                <table className={`text-sm leading-3 bg-white w-full mb-2`}>
                  <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="text-center">Kategori Kantor</th>
                      {data.months.map((month) => (
                        <th className="text-center">
                          {`${month} ${Object.values(data.gap_toners)[0].idecice_date !==
                            undefined
                            ? new Date(
                              Object.values(data.gap_toners)[0].idecice_date
                            ).getFullYear()
                            : new Date().getFullYear()
                            }`}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="overflow-y-auto">
                    {Object.entries(groupBy(data.gap_toners, "kategori")).map(
                      ([key, values]) => (
                        <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200 divide-x divide-slate-200">
                          <td>{key}</td>
                          {data.months.map((month) => {
                            return (
                              <td className="text-right">
                                {values
                                  .filter(
                                    (value) =>
                                      new Date(
                                        value.idecice_date
                                      ).toLocaleString("en-US", {
                                        month: "long",
                                      }) === month
                                  )
                                  .reduce((acc, toner) => {
                                    return acc + toner.total;
                                  }, 0)
                                  .toLocaleString("id-ID")}
                              </td>
                            );
                          })}
                        </tr>
                      )
                    )}
                  </tbody>
                </table>
              </div>
              <h2 className="mt-2 text-lg font-semibold">
                Quantity Per Cabang
              </h2>
              <div className="mt-2 overflow-x-auto">
                <table className={`text-sm leading-3 bg-white w-full mb-2`}>
                  <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="text-center">Kategori Kantor</th>
                      {data.months.map((month) => (
                        <th className="text-center">
                          {`${month} ${Object.values(data.gap_toners)[0].idecice_date !==
                            undefined
                            ? new Date(
                              Object.values(data.gap_toners)[0].idecice_date
                            ).getFullYear()
                            : new Date().getFullYear()
                            }`}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="overflow-y-auto">
                    {Object.entries(groupBy(data.gap_toners, "cabang")).map(
                      ([key, values]) => (
                        <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200 divide-x divide-slate-200">
                          <td>{key}</td>
                          {data.months.map((month) => {
                            console.log(values);
                            return (
                              <td className="text-center">
                                {values
                                  .filter(
                                    (value) =>
                                      new Date(
                                        value.idecice_date
                                      ).toLocaleString("en-US", {
                                        month: "long",
                                      }) === month
                                  )
                                  .reduce((acc, toner) => {
                                    return acc + toner.quantity;
                                  }, 0)
                                  .toLocaleString("id-ID")}
                              </td>
                            );
                          })}
                        </tr>
                      )
                    )}
                  </tbody>
                </table>
              </div>
              <h2 className="mt-2 text-lg font-semibold">Nominal Per Cabang</h2>
              <div className="mt-2 overflow-x-auto">
                <table className={`text-sm leading-3 bg-white w-full`}>
                  <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="text-center">Kategori Kantor</th>
                      {data.months.map((month) => (
                        <th className="text-center">
                          {`${month} ${Object.values(data.gap_toners)[0].idecice_date !==
                            undefined
                            ? new Date(
                              Object.values(data.gap_toners)[0].idecice_date
                            ).getFullYear()
                            : new Date().getFullYear()
                            }`}
                        </th>
                      ))}
                    </tr>
                  </thead>
                  <tbody className="overflow-y-auto">
                    {Object.entries(groupBy(data.gap_toners, "cabang")).map(
                      ([key, values]) => (
                        <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200 divide-x divide-slate-200">
                          <td>{key}</td>
                          {data.months.map((month) => {
                            console.log(values);
                            return (
                              <td className="text-right">
                                {values
                                  .filter(
                                    (value) =>
                                      new Date(
                                        value.idecice_date
                                      ).toLocaleString("en-US", {
                                        month: "long",
                                      }) === month
                                  )
                                  .reduce((acc, toner) => {
                                    return acc + toner.total;
                                  }, 0)
                                  .toLocaleString("id-ID")}
                              </td>
                            );
                          })}
                        </tr>
                      )
                    )}
                  </tbody>
                </table>
              </div>
            </div>
          )}
          {active === "kdo" && (
            <DataTable
              columns={columnsKdo}
              fetchUrl={"/api/inquery/kdos"}
              bordered={true}
              parameters={{ branch_id: auth.user.branch_id }}
            />
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

import Alert from "@/Components/Alert";
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

export default function Page({ sessions, auth, data, type_names, yearToner }) {
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
    datePickerValue
  } = useFormContext();

  const toggleModalCreate = (id) => {
    setInitialData({ disclaimer: null });
    setUrl("gap.stos.store.hasil_sto");
    setId(id);

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
  const columnTonerCosts = [
    {
      name: "Cabang",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.toners.detail", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },

    {
      name: "January",
      field: "january",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.january.toLocaleString("id-ID"),
    },
    {
      name: "February",
      field: "february",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.february.toLocaleString("id-ID"),
    },
    {
      name: "March",
      field: "march",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.march.toLocaleString("id-ID"),
    },
    {
      name: "April",
      field: "April",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.april.toLocaleString("id-ID"),
    },
    {
      name: "May",
      field: "may",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.may.toLocaleString("id-ID"),
    },
    {
      name: "June",
      field: "june",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.june.toLocaleString("id-ID"),
    },
    {
      name: "July",
      field: "july",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.july.toLocaleString("id-ID"),
    },
    {
      name: "August",
      field: "august",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.august.toLocaleString("id-ID"),
    },
    {
      name: "September",
      field: "september",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.september.toLocaleString("id-ID"),
    },
    {
      name: "October",
      field: "october",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.october.toLocaleString("id-ID"),
    },
    {
      name: "November",
      field: "november",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.november.toLocaleString("id-ID"),
    },
    {
      name: "December",
      field: "december",
      type: "custom",
      className: "text-right tabular-nums",
      agg: "sum",
      format: "currency",
      render: (data) => data.december.toLocaleString("id-ID"),
    },
  ];
  const columnTonerQuantities = [
    {
      name: "Cabang",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.toners.detail", data.slug)}>
          {data.branch_name}
        </Link>
      ),
    },

    {
      name: "January",
      field: "january",
      agg: "sum",
    },
    {
      name: "February",
      field: "february",
      agg: "sum",
    },
    {
      name: "March",
      field: "march",
      agg: "sum",
    },
    {
      name: "April",
      field: "April",
      agg: "sum",
    },
    {
      name: "May",
      field: "may",
      agg: "sum",
    },
    {
      name: "June",
      field: "june",
      agg: "sum",
    },
    {
      name: "July",
      field: "july",
      agg: "sum",
    },
    {
      name: "August",
      field: "august",
      agg: "sum",
    },
    {
      name: "September",
      field: "september",
      agg: "sum",
    },
    {
      name: "October",
      field: "october",
      agg: "sum",
    },
    {
      name: "November",
      field: "november",
      agg: "sum",
    },
    {
      name: "December",
      field: "december",
      agg: "sum",
    },
  ];

  const columns = [
    {
      name: "Nama",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link href={route("inquery.assets.detail", {
          slug: data.slug,
          ...datePickerValue
        })}>
          {data.branch_name}
        </Link>
      ),
    },
    {
      name: "Tipe Cabang",
      field: "type_name",
      filterable: true,
    },
    {
      name: "Item",
      field: "item.depre",
      className: "text-center",
    },
    {
      name: "Nilai Perolehan",
      field: "nilai_perolehan.depre",
      className: "text-right tabular-nums",
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
      className: "text-right tabular-nums",
      format: "currency",
      type: "custom",
      render: (data) => {
        return data.penyusutan.depre.toLocaleString("id-ID");
      },
    },
    {
      name: "Net Book Value",
      field: "net_book_value.depre",
      className: "text-right tabular-nums",
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
      className: "text-right tabular-nums",
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
      className: "text-right tabular-nums",
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
        <Link
          href={route("gap.kdos.mobil", {
            slug: data.branches.slug,
            periode: data.periode,
          })}
        >
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
        <Link
          href={route("inquery.assets.sto", {
            slug: data.slug,
          })}
        >
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
            href={`/storage/gap/stos/${data.slug}/${data.periode}/${data.semester}/${data.disclaimer}`}
            target="__blank"
          >
            {" "}
            {data.disclaimer}
          </a>
        ) : (
          <Button
            onClick={(e) => toggleModalCreate(data.slug)}
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
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
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
              parameters={{ branch_id: auth.user.branch_id, ...datePickerValue }}
              headings={headings}
              bordered={true}
              periodic={true}
              component={[
                {
                  data: type_names,
                  field: "type_name",
                },
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
                        BSM dan BSO menyatakan sudah melakukan Stock Opname ini
                        dengan benar dan bertanggung jawab jika dikemudian hari
                        diketahui ada data yang tidak sesuai.
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
            <>
              <h2 className="mt-2 text-lg font-semibold">
                Quantity Per Cabang Tahun {yearToner}
              </h2>
              <DataTable
                columns={columnTonerQuantities}
                fetchUrl={"/api/inquery/toners"}
                refreshUrl={isRefreshed}
                bordered={true}
                parameters={{
                  branch_id: auth.user.branch_id,
                  type: "quantity",
                }}
              />
              <h2 className="mt-2 text-lg font-semibold">
                Nominal Per Cabang Tahun {yearToner}
              </h2>
              <DataTable
                columns={columnTonerCosts}
                fetchUrl={"/api/inquery/toners"}
                refreshUrl={isRefreshed}
                bordered={true}
                parameters={{ branch_id: auth.user.branch_id, type: "nominal" }}
              />
            </>
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

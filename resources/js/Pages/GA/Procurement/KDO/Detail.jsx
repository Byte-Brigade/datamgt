import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import {
  DocumentPlusIcon
} from "@heroicons/react/24/outline";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head, useForm } from "@inertiajs/react";
import {
  Button,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
  Input,
  Option,
  Select,
  Typography,
} from "@material-tailwind/react";
import { useState } from "react";

export default function Detail({
  auth,
  sessions,
  kdo_mobil,
  years,
  months,
  periode,
  vendor
}) {
  console.log(kdo_mobil);
  const currentDate = new Date();
  const initialData = {
    id: 0,
    branch_id: 0,
    gap_kdo_id: 0,
    vendor: null,
    nopol: null,
    awal_sewa: null,
    akhir_sewa: null,
    periode: null,
    biaya_sewa: 0,
  };

  const {
    data,
    setData,
    post,
    put,
    delete: destroy,
    processing,
    errors,
  } = useForm(initialData);
  const [isModalCreateOpen, setIsModalCreateOpen] = useState(false);
  const [isModalImportOpen, setIsModalImportOpen] = useState(false);
  const [isModalEditOpen, setIsModalEditOpen] = useState(false);
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);
  const [periodeVal, setPeriodeVal] = useState(0);




  const handleSubmitCreate = (e) => {
    e.preventDefault();
    post(route("gap.kdos.mobil.store", data.branch_id), {
      method: "post",
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalCreateOpen(!isModalCreateOpen);
      },
    });
  };

  const handleSubmitImport = (e) => {
    e.preventDefault();
    post(route("gap.kdos.import"), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalImportOpen(!isModalImportOpen);
      },
    });
  };

  const handleExport = (e) => {
    const { gap_kdo_id } = data;
    e.preventDefault();
    window.open(
      route("gap.kdos.mobil.export", kdo_mobil.branches.branch_code) +
      `?gap_kdo_id=${gap_kdo_id}`,
      "_self"
    );
  };
  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("gap.kdos.mobil.update", data.id), {
      method: "put",
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalEditOpen(!isModalEditOpen);
      },
    });
  };

  const handleSubmitDelete = (e) => {
    e.preventDefault();
    destroy(
      route("gap.kdos.mobil.destroy", {
        id: data.id,
        periode: data.periode,
      }),
      {
        replace: true,
        onFinish: () => {
          setIsRefreshed(!isRefreshed);
          setIsModalDeleteOpen(!isModalDeleteOpen);
        },
      }
    );
  };

  const getPeriode = (month, year) => {
    // Create a date with the given month and year (subtract 1 from the month as months are 0-indexed in JavaScript)
    let date = new Date(Date.UTC(year, month - 1, 1));

    // Format the date to get the ISO string for the 1st day of the month
    return date.toISOString().slice(0, 10);
  };

  const toggleModalCreate = () => {
    setIsModalCreateOpen(!isModalCreateOpen);
  };
  const toggleModalImport = () => {
    setIsModalImportOpen(!isModalImportOpen);
  };
  const toggleModalEdit = () => {
    setIsModalEditOpen(!isModalEditOpen);
  };

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  const columns = [
    { name: "Vendor", field: "vendor", sortable: true },
    { name: "Cabang", field: "branches.branch_name", sortable: true },
    { name: "Nopol", field: "nopol", className: "" },
    {
      name: "Awal Sewa",
      type: "date",
      field: "awal_sewa",
      sortable: true,
      className: "",
    },
    {
      name: "Akhir Sewa",
      type: "date",
      field: "akhir_sewa",
      sortable: true,
      className: "",
    },
    {
      name: "Biaya Sewa",
      field: "biaya_sewa",
      type: "custom",

      className: "text-center ",
      render: (data) => {
        return data.biaya_sewa
          ? data.biaya_sewa.toLocaleString("ID-id")
          : "-";
      },
    },
    {
      name: "Action",
      field: "action",
      className: "text-center",
      render: (data) => (
        <DropdownMenu
          placement="left-start"
          onEditClick={() => {
            toggleModalEdit();
            setData(data);
          }}
          onDeleteClick={() => {
            toggleModalDelete();
            setData(data);
          }}
        />
      ),
    },
  ];
  console.log(kdo_mobil);

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | KDO Mobil" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <h2 className="text-xl font-semibold text-end mb-4">
            {kdo_mobil.branches.branch_name}
          </h2>
          {hasRoles("superadmin|admin|procurement", auth) &&
            ["can add", "can export"].some((permission) =>
              auth.permissions.includes(permission)
            ) && (
              <div className="flex items-center justify-between mb-4">
                {auth.permissions.includes("can add") && (

                  <PrimaryButton
                    className="bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
                    onClick={toggleModalImport}
                  >
                    <div className="flex items-center gap-x-2">
                      <DocumentPlusIcon className="w-4 h-4" />
                      Import Excel
                    </div>
                  </PrimaryButton>

                )}
              </div>
            )}
          <DataTable
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("superadmin|admin|procurement", auth) &&
                ["can edit", "can delete"].some((permission) =>
                  auth.permissions.includes(permission)
                )
                : true
            )}
            fetchUrl={`/api/gap/kdos/detail/${kdo_mobil.branch_id}`}
            refreshUrl={isRefreshed}
            periodic={true}
            parameters={{ periode, vendor }}
          />
        </div>
      </div>
      {/* Modal Create */}
      <Dialog open={isModalCreateOpen} handler={toggleModalCreate} size="md">
        <DialogHeader className="flex items-center justify-between">
          Tambah Data
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
        <form onSubmit={handleSubmitCreate}>
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Input
                label="Vendor"
                value={data.vendor || ""}
                disabled={processing}
                onChange={(e) => setData("vendor", e.target.value)}
              />
              <Input
                label="Nopol"
                value={data.nopol || ""}
                disabled={processing}
                onChange={(e) => setData("nopol", e.target.value)}
              />
              <Input
                label="Awal Sewa"
                value={data.awal_sewa || ""}
                type="date"
                disabled={processing}
                onChange={(e) => setData("awal_sewa", e.target.value)}
              />
              <Input
                label="Akhir Sewa"
                value={data.akhir_sewa || ""}
                type="date"
                disabled={processing}
                onChange={(e) => setData("akhir_sewa", e.target.value)}
              />
              {/* <Select
                label="Tahun"
                value={`${data.year}`}
                onChange={(e) => setData("year", e)}
              >
                {years.map((year, index) => (
                  <Option key={index} value={`${year}`}>
                    {year}
                  </Option>
                ))}
              </Select> */}
              <Select
                label="Bulan"
                value={`${data.month || ""}`}
                onChange={(e) => handlePeriode(e)}
              >
                {months.map((month, index) => (
                  <Option key={index} value={`${index + 1}`}>
                    {month}
                  </Option>
                ))}
              </Select>
              <Input
                label="Biaya Sewa"
                value={data.biaya_sewa || ""}
                type="number"
                disabled={processing}
                onChange={(e) => setData("biaya_sewa", e.target.value)}
              />
            </div>
          </DialogBody>
          <DialogFooter>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={processing} type="submit">
                Tambah
              </Button>
              <SecondaryButton type="button" onClick={toggleModalCreate}>
                Tutup
              </SecondaryButton>
            </div>
          </DialogFooter>
        </form>
      </Dialog>
       {/* Modal Import */}
       <Dialog open={isModalImportOpen} handler={toggleModalImport} size="md">
        <DialogHeader className="flex items-center justify-between">
          Import Data
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={toggleModalImport}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <form onSubmit={handleSubmitImport} encType="multipart/form-data">
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Input
                variant="standard"
                label="Import Excel (.xlsx)"
                disabled={processing}
                type="file"
                name="import"
                id="import"
                accept=".xlsx"
                onChange={(e) => setData("file", e.target.files[0])}
              />
            </div>
          </DialogBody>
          <DialogFooter className="w-100 flex justify-between">
            <SecondaryButton type="button">
              <a href={route("gap.kdos.mobil.template")}>Download Template</a>
            </SecondaryButton>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={processing} type="submit">
                Simpan
              </Button>
              <SecondaryButton type="button" onClick={toggleModalImport}>
                Tutup
              </SecondaryButton>
            </div>
          </DialogFooter>
        </form>
      </Dialog>
      {/* Modal Edit */}
      <Dialog open={isModalEditOpen} handler={toggleModalEdit} size="md">
        <DialogHeader className="flex items-center justify-between">
          Ubah Data
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={toggleModalEdit}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <form onSubmit={handleSubmitEdit}>
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Input
                label="Vendor"
                value={data.vendor || ""}
                disabled={processing}
                onChange={(e) => setData("vendor", e.target.value)}
              />
              <Input
                label="Nopol"
                value={data.nopol || ""}
                disabled={processing}
                onChange={(e) => setData("nopol", e.target.value)}
              />
              <Input
                label="Awal Sewa"
                value={data.awal_sewa || ""}
                type="date"
                disabled={processing}
                onChange={(e) => setData("awal_sewa", e.target.value)}
              />
              <Input
                label="Akhir Sewa"
                value={data.akhir_sewa || ""}
                type="date"
                disabled={processing}
                onChange={(e) => setData("akhir_sewa", e.target.value)}
              />
              <Input
                label="Periode"
                value={data.periode || ""}
                type="month"
                disabled={processing}
                onChange={(e) => setData("periode", e.target.value)}
              />
              <Input
                label="Biaya Sewa"
                value={data.biaya_sewa || ""}
                type="number"
                disabled={processing}
                onChange={(e) => setData("biaya_sewa", e.target.value)}
              />
            </div>
          </DialogBody>
          <DialogFooter>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={processing} type="submit">
                Ubah
              </Button>
              <SecondaryButton type="button" onClick={toggleModalEdit}>
                Tutup
              </SecondaryButton>
            </div>
          </DialogFooter>
        </form>
      </Dialog>
      {/* Modal Delete */}
      <Dialog open={isModalDeleteOpen} handler={toggleModalDelete} size="md">
        <DialogHeader className="flex items-center justify-between">
          Hapus Data
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={toggleModalDelete}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <DialogBody divider>
          <Typography>
            Apakah anda yakin ingin menghapus{" "}
            <span className="text-lg font-bold">{data.id}</span> ?
          </Typography>
        </DialogBody>
        <DialogFooter>
          <form
            onSubmit={handleSubmitDelete}
            className="flex flex-row-reverse gap-x-4"
          >
            <Button color="red" disabled={processing} type="submit">
              Hapus
            </Button>
            <SecondaryButton type="button" onClick={toggleModalDelete}>
              Tutup
            </SecondaryButton>
          </form>
        </DialogFooter>
      </Dialog>
    </AuthenticatedLayout>
  );
}

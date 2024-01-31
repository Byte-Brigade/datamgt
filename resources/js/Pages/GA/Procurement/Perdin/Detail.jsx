import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { DocumentArrowDownIcon } from "@heroicons/react/24/outline";
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
  Typography,
} from "@material-tailwind/react";
import { useState } from "react";

export default function Detail({
  auth,
  sessions,
  divisi_pembebanan,
  years,
  months,
}) {
  const currentDate = new Date();
  const initialData = {
    id: null,
    branch_id: 0,
    gap_kdo_id: 0,
    vendor: null,
    nopol: null,
    awal_sewa: null,
    akhir_sewa: null,
    year: null,
    month: null,
    biaya_sewas: null,
    biaya_sewa: null,
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
    post(route("gap.kdo.mobil.store", data.branch_id), {
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
    post(route("gap.kdo.mobil.import"), {
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
      route("gap.kdo.mobil.export") + `?gap_kdo_id=${gap_kdo_id}`,
      "_self"
    );
  };
  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("gap.kdo.mobil.update", data.id), {
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
      route("gap.kdo.mobil.destroy", {
        id: data.id,
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

  const handlePeriode = (month, year) => {
    let biaya_sewas = data.biaya_sewas;

    if (!Array.isArray(biaya_sewas)) {
      setData("biaya_sewa", 0);
      return;
    }
    let biaya_sewa = biaya_sewas.find(
      (item) => item.periode === getPeriode(month, year)
    );
    setData({
      ...data,
      month: month,
      year: year,
      biaya_sewa: biaya_sewa ? biaya_sewa : 0,
    });
    console.log(data.month);
    console.log(data.year);
  };
  const handleMonth = (e) => {
    handlePeriode(e, data.year);
  };
  const handleYear = (e) => {
    setData("year", e);
    handlePeriode(data.month, e);
  };
  const handleBiayaSewa = (val) => {
    if (typeof data.biaya_sewa === "object" && data.biaya_sewa !== null) {
      let biaya_sewa = { ...data.biaya_sewa, value: val };
      console.log("handle");
      console.log(biaya_sewa);
      setData("biaya_sewa", biaya_sewa);
      return;
    }
    setData("biaya_sewa", Number(val));

    console.log(data.biaya_sewa);
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
    {
      name: "Periode",
      field: "periode",
    },
    {
      name: "Airline",
      field: "airline",
      className: "text-right",
      type: "custom",
      agg: "sum",
      format: "currency",

      render: (data) => data.airline.toLocaleString("id-ID"),
    },
    {
      name: "KA",
      field: "ka",
      className: "text-right",
      type: "custom",
      agg: "sum",
      format: "currency",
      render: (data) => data.ka.toLocaleString("id-ID"),
    },
    {
      name: "Hotel",
      field: "hotel",
      className: "text-right",
      type: "custom",
      agg: "sum",
      format: "currency",
      render: (data) => data.hotel.toLocaleString("id-ID"),
    },
    {
      name: "Total",
      field: "total",
      className: "text-right",
      type: "custom",
      agg: "sum",
      format: "currency",
      render: (data) => data.total.toLocaleString("id-ID"),
    },
  ];
  console.log(data.month);

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | Biaya Perjalanan Dinas" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex items-center justify-between mb-4">
            <h2 className="text-xl font-semibold text-center">
              {divisi_pembebanan}
            </h2>
          </div>
          <DataTable
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("superadmin|admin|procurement", auth) &&
                  ["can edit", "can delete"].some((permission) =>
                    auth.permissions.includes(permission)
                  )
                : true
            )}
            fetchUrl={`/api/gap/perdin/${divisi_pembebanan}`}
            refreshUrl={isRefreshed}
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
              <Button
                className="flex items-center gap-x-2 max-w-fit"
                size="sm"
                onClick={handleExport}
              >
                <DocumentArrowDownIcon className="w-5 h-5" />
                Download Template
              </Button>
              <Input
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
          <DialogFooter>
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
                label="Biaya Sewa"
                value={data.biaya_sewa ? data.biaya_sewa.value : ""}
                type="number"
                disabled={processing}
                onChange={(e) => handleBiayaSewa(e.target.value)}
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

import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentPlusIcon } from "@heroicons/react/24/outline";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head, Link, useForm } from "@inertiajs/react";
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

export default function Page({ auth, sessions }) {
  const initialData = {
    jumlah_kendaraan: null,
    jumlah_driver: null,
    sewa_kendaraan: null,
    biaya_driver: null,
    ot: null,
    rfid: null,
    non_rfid: null,
    grab: null,
    periode: null,
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

  const [isModalImportOpen, setIsModalImportOpen] = useState(false);
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isModalCreateOpen, setIsModalCreateOpen] = useState(false);
  const [isModalEditOpen, setIsModalEditOpen] = useState(false);
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);

  const columns = [
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
      name: "Disclaimer",
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
          "-"
        ),
    },
  ];

  const footerCols = [{ name: "Sum", span: 5 }, { name: 123123123 }];

  const handleSubmitImport = (e) => {
    e.preventDefault();
    post(route("gap.stos.import"), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalImportOpen(!isModalImportOpen);
      },
    });
  };

  const handleSubmitExport = (e) => {
    const { branch } = data;
    e.preventDefault();
    window.open(route("gap.stos.export"), "_self");
    setIsModalExportOpen(!isModalExportOpen);
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("gap.stos.update", data.id), {
      method: "put",
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalEditOpen(!isModalEditOpen);
      },
    });
  };
  const handleSubmitCreate = (e) => {
    e.preventDefault();
    post(route("gap.stos.store"), {
      method: "post",
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalCreateOpen(!isModalCreateOpen);
      },
    });
  };

  const handleSubmitDelete = (e) => {
    e.preventDefault();
    destroy(route("gap.stos.delete", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalDeleteOpen(!isModalDeleteOpen);
      },
    });
  };

  const toggleModalImport = () => {
    setIsModalImportOpen(!isModalImportOpen);
  };

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  const toggleModalEdit = () => {
    setIsModalEditOpen(!isModalEditOpen);
  };
  const toggleModalCreate = () => {
    setIsModalCreateOpen(!isModalCreateOpen);
  };

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | KDO" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          {hasRoles("superadmin|procurement", auth) &&
            auth.permissions.includes("can export") && (
              <div className="flex items-center justify-between mb-4">
                <div>
                  <PrimaryButton
                    className="bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
                    onClick={toggleModalImport}
                  >
                    <div className="flex items-center gap-x-2">
                      <DocumentPlusIcon className="w-4 h-4" />
                      <a href={route("gap.stos.disclaimer")}>
                        Download Disclaimer
                      </a>
                    </div>
                  </PrimaryButton>
                </div>
                <PrimaryButton onClick={toggleModalExport}>
                  Create Report
                </PrimaryButton>
              </div>
            )}
          <DataTable
            columns={columns}
            fetchUrl={"/api/gap/stos"}
            refreshUrl={isRefreshed}
            bordered={true}
          />
        </div>
      </div>
      {/* Modal Import */}
      {/* <Dialog open={isModalImportOpen} handler={toggleModalImport} size="md">
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
              <a href={route("gap.stos.template")}>Download Template</a>
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
      </Dialog> */}
      {/* Modal Export */}
      <Dialog open={isModalExportOpen} handler={toggleModalExport} size="md">
        <DialogHeader className="flex items-center justify-between">
          Create Report
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={toggleModalExport}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <DialogBody divider>
          <div className="flex flex-col gap-y-4">
            <Typography>Buat Report Data STO?</Typography>
          </div>
        </DialogBody>
        <DialogFooter>
          <div className="flex flex-row-reverse gap-x-4">
            <Button
              onClick={handleSubmitExport}
              disabled={processing}
              type="submit"
            >
              Buat
            </Button>
            <SecondaryButton type="button" onClick={toggleModalExport}>
              Tutup
            </SecondaryButton>
          </div>
        </DialogFooter>
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
                label="Jangka Waktu (Expired Date)"
                value={data.expired_date || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("expired_date", e.target.value)}
              />
              <Input
                label="Keterangan"
                value={data.keterangan || ""}
                disabled={processing}
                onChange={(e) => setData("keterangan", e.target.value)}
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
          <DialogBody className="overflow-y-scroll max-h-96" divider>
            <div className="flex flex-col gap-y-4">
              <Input
                label="Divisi Pembebanan"
                value={data.divisi_pembebanan || ""}
                disabled={processing}
                onChange={(e) => setData("divisi_pembebanan", e.target.value)}
              />
              <Input
                label="Category"
                value={data.category || ""}
                disabled={processing}
                onChange={(e) => setData("divisi_pembebanan", e.target.value)}
              />
              <Input
                label="Tipe"
                value={data.category || ""}
                disabled={processing}
                onChange={(e) => setData("divisi_pembebanan", e.target.value)}
              />
              <Input
                label="Jumlah Driver"
                value={data.jumlah_driver || ""}
                disabled={processing}
                onChange={(e) => setData("jumlah_driver", e.target.value)}
              />
              <Input
                label="Sewa Kendaraan"
                value={data.sewa_kendaraan || ""}
                disabled={processing}
                onChange={(e) => setData("sewa_kendaraan", e.target.value)}
              />
              <Input
                label="Biaya Driver"
                value={data.biaya_driver || ""}
                disabled={processing}
                onChange={(e) => setData("biaya_driver", e.target.value)}
              />
              <Input
                label="OT"
                value={data.ot || ""}
                disabled={processing}
                onChange={(e) => setData("ot", e.target.value)}
              />
              <Input
                label="RFID"
                value={data.rfid || ""}
                disabled={processing}
                onChange={(e) => setData("rfid", e.target.value)}
              />
              <Input
                label="NON RFID"
                value={data.non_rfid || ""}
                disabled={processing}
                onChange={(e) => setData("non_rfid", e.target.value)}
              />
              <Input
                label="GRAB"
                value={data.grab || ""}
                disabled={processing}
                onChange={(e) => setData("grab", e.target.value)}
              />
              <Input
                label="Periode"
                value={data.expired_date || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("periode", e.target.value)}
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
            <span className="text-lg font-bold"></span> ?
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

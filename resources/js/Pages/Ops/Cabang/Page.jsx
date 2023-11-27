import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentArrowDownIcon, DocumentPlusIcon } from "@heroicons/react/24/outline";
import { PlusIcon, XMarkIcon } from "@heroicons/react/24/solid";
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
  Radio,
  Select,
  Typography,
} from "@material-tailwind/react";
import { useState } from "react";

export default function Cabang({ auth, sessions, branch_types, branches }) {
  const initialData = {
    file: null,
    branch_code: null,
    branch_name: null,
    address: null,
    branch_type_id: null,
    layanan_atm: null,
    npwp: null,
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
  const [isModalUploadOpen, setIsModalUploadOpen] = useState(false);
  const [isModalCreateOpen, setIsModalCreateOpen] = useState(false);
  const [isModalEditOpen, setIsModalEditOpen] = useState(false);
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);
  const [open, setOpen] = useState(false);
  const columns = [
    { name: "Kode Cabang", field: "branch_code" },
    {
      name: "Tipe Cabang",
      field: "branch_types.type_name",
      sortable: false,
      filterable: true,
    },
    { name: "Nama Cabang", field: "branch_name"},
    { name: "NPWP", field: "npwp" },
    { name: "Area", field: "area", className: "text-center" },
    { name: "Alamat", field: "address", className: "w-[300px]" },
    { name: "No. Telpon", field: "telp" },
    { name: "Fasilitas ATM", field: "fasilitas_atm" },
    {
      name: "Layanan ATM",
      field: "layanan_atm",
      filterable: true,
      component: "branches",
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

  const handleSubmitImport = (e) => {
    e.preventDefault();
    post(route("branches.import"), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalImportOpen(!isModalImportOpen);
      },
    });
  };

  const handleSubmitUpload = (e) => {
    e.preventDefault();
    post(route("branches.upload", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalUploadOpen(!isModalUploadOpen);
      },
    });
  };

  const handleSubmitExport = (e) => {
    e.preventDefault();
    setIsModalExportOpen(!isModalExportOpen);
    window.open(route("branches.export"), "_self");
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("branches.update", data.id), {
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
    post(route("branches.store", data.id), {
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
    destroy(route("branches.delete", data.id), {
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

  const toggleModalUpload = () => {
    setIsModalUploadOpen(!isModalUploadOpen);
  };

  const toggleModalCreate = () => {
    setData(initialData);
    setIsModalCreateOpen(!isModalCreateOpen);
  };
  const toggleModalEdit = () => {
    setIsModalEditOpen(!isModalEditOpen);
  };

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <BreadcrumbsDefault />
      <Head title="Data Cabang" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          {hasRoles("branch_ops|superadmin", auth) &&
            ["can add", "can export"].some((permission) =>
              auth.permissions.includes(permission)
            ) && (
              <div className="flex items-center justify-between mb-4">
                {auth.permissions.includes("can add") && (
                  <div>
                    <PrimaryButton
                      className="mr-2 bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
                      onClick={toggleModalCreate}
                    >
                      <div className="flex items-center gap-x-2">
                        <PlusIcon className="w-4 h-4" />
                        Add
                      </div>
                    </PrimaryButton>
                    <PrimaryButton
                      className="bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
                      onClick={toggleModalImport}
                    >
                      <div className="flex items-center gap-x-2">
                        <DocumentPlusIcon className="w-4 h-4" />
                        Import Excel
                      </div>
                    </PrimaryButton>
                  </div>
                )}
                {auth.permissions.includes("can export") && (
                  <PrimaryButton onClick={toggleModalExport}>
                  <div className="flex items-center gap-x-2">
                    <DocumentArrowDownIcon className="w-4 h-4" />
                    Create Report
                  </div>
                </PrimaryButton>
                )}
              </div>
            )}
          <DataTable
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("branch_ops|superadmin", auth) &&
                  ["can edit", "can delete"].some((permission) =>
                    auth.permissions.includes(permission)
                  )
                : true
            )}
            fetchUrl={"/api/ops/branches"}
            refreshUrl={isRefreshed}
            className="w-[1500px]"
            component={[
              {
                data: Array.from(
                  new Set(branches.map((branch) => branch.layanan_atm))
                ),
                field: "layanan_atm",
              },
              {
                data: Array.from(
                  new Set(
                    branch_types
                      .filter((type) =>
                        ["KC", "KCP", "KF"].includes(type.type_name)
                      )
                      .map((type) => type.type_name)
                  )
                ),
                field: "branch_types.type_name",
              },
            ]}
          />
        </div>
      </div>
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
      {/* Modal Upload */}
      <Dialog open={isModalUploadOpen} handler={toggleModalUpload} size="md">
        <DialogHeader className="flex items-center justify-between">
          Upload Lampiran
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={toggleModalUpload}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <form onSubmit={handleSubmitUpload} encType="multipart/form-data">
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Input
                variant="standard"
                label="Upload Photo"
                disabled={processing}
                type="file"
                name="upload"
                id="upload"
                accept=".jpg,.jpeg,.png"
                onChange={(e) => setData("photo", e.target.files[0])}
              />
            </div>
          </DialogBody>
          <DialogFooter>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={processing} type="submit">
                Simpan
              </Button>
              <SecondaryButton type="button" onClick={toggleModalUpload}>
                Tutup
              </SecondaryButton>
            </div>
          </DialogFooter>
        </form>
      </Dialog>
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
            <Typography>Buat Report Data Cabang?</Typography>
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
              <Select
                label="Tipe Cabang"
                value={`${data.branch_type_id || ""}`}
                disabled={processing}
                onChange={(e) => setData("branch_type_id", e)}
              >
                {branch_types.map((type) => (
                  <Option key={type.id} value={`${type.id}`}>
                    {type.type_name}
                  </Option>
                ))}
              </Select>
              <Input
                label="Kode Cabang"
                value={data.branch_code}
                disabled={processing}
                onChange={(e) => setData("branch_code", e.target.value)}
              />
              <Input
                label="Nama Cabang"
                value={data.branch_name}
                disabled={processing}
                onChange={(e) => setData("branch_name", e.target.value)}
              />
              <Input
                label="Alamat"
                value={data.address}
                disabled={processing}
                onChange={(e) => setData("address", e.target.value)}
              />
              <Input
                label="Telp"
                value={data.telp}
                disabled={processing}
                onChange={(e) => setData("telp", e.target.value)}
              />
              <Input
                label="NPWP"
                value={data.npwp}
                disabled={processing}
                onChange={(e) => setData("npwp", e.target.value)}
              />
              <div className="flex flex-col">
                <span className="text-sm font-light">Fasilitas ATM</span>
                <div className="flex gap-x-4">
                  <Radio
                    name="layanan_atm"
                    label="24 Jam"
                    checked={data.layanan_atm === "24 Jam"}
                    value="24 Jam"
                    onChange={(e) => setData("layanan_atm", e.target.value)}
                  />
                  <Radio
                    name="layanan_atm"
                    label="Jam Operasional"
                    checked={data.layanan_atm === "Jam Operasional"}
                    value="Jam Operasional"
                    onChange={(e) => setData("layanan_atm", e.target.value)}
                  />
                  <Radio
                    name="layanan_atm"
                    label="Tidak Ada"
                    checked={
                      data.layanan_atm === null || data.layanan_atm === ""
                    }
                    value=""
                    onChange={(e) => setData("layanan_atm", e.target.value)}
                  />
                </div>
              </div>
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
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Select
                label="Tipe Cabang"
                value={`${data.branch_type_id || ""}`}
                disabled={processing}
                onChange={(e) => setData("branch_type_id", e)}
              >
                {branch_types.map((type) => (
                  <Option key={type.id} value={`${type.id}`}>
                    {type.type_name}
                  </Option>
                ))}
              </Select>
              <Input
                label="Kode Cabang"
                value={data.branch_code}
                disabled={processing}
                onChange={(e) => setData("branch_code", e.target.value)}
              />
              <Input
                label="Nama Cabang"
                value={data.branch_name}
                disabled={processing}
                onChange={(e) => setData("branch_name", e.target.value)}
              />
              <Input
                label="Alamat"
                value={data.address}
                disabled={processing}
                onChange={(e) => setData("address", e.target.value)}
              />
              <Input
                label="Telp"
                value={data.telp}
                disabled={processing}
                onChange={(e) => setData("telp", e.target.value)}
              />
              <Input
                label="NPWP"
                value={data.npwp}
                disabled={processing}
                onChange={(e) => setData("npwp", e.target.value)}
              />
              <div className="flex flex-col">
                <span className="text-sm font-light">Fasilitas ATM</span>
                <div className="flex gap-x-4">
                  <Radio
                    name="layanan_atm"
                    label="24 Jam"
                    checked={data.layanan_atm === "24 Jam"}
                    value="24 Jam"
                    onChange={(e) => setData("layanan_atm", e.target.value)}
                  />
                  <Radio
                    name="layanan_atm"
                    label="Jam Operasional"
                    checked={data.layanan_atm === "Jam Operasional"}
                    value="Jam Operasional"
                    onChange={(e) => setData("layanan_atm", e.target.value)}
                  />
                  <Radio
                    name="layanan_atm"
                    label="Tidak Ada"
                    checked={
                      data.layanan_atm === null || data.layanan_atm === ""
                    }
                    value=""
                    onChange={(e) => setData("layanan_atm", e.target.value)}
                  />
                </div>
              </div>
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
            <span className="text-lg font-bold">
              {data.branch_code} - {data.branch_name}
            </span>{" "}
            ?
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

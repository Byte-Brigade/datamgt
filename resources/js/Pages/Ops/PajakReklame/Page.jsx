import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Reports/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentPlusIcon, ArrowUpTrayIcon, DocumentArrowDownIcon } from "@heroicons/react/24/outline";
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
  Select,
  Typography,
} from "@material-tailwind/react";
import { useState } from "react";

export default function PajakReklame({ auth, branches, sessions }) {
  const initialData = {
    file: null,
    branch_id: 0,
    branches: {
      branch_code: null,
      branch_name: null,
    },
    periode_awal: null,
    periode_akhir: null,
    note: null,
    no_izin: null,
    nilai_pajak: null,
    file_izin_reklame: null,
    file_skpd: null,
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
  const [fileType, setFileType] = useState("file");
  const columns = [
    { name: "Kode Cabang", field: "branches.branch_code", sortable: true },
    { name: "Nama Cabang", field: "branches.branch_name", sortable: true },
    {
      name: "Periode Awal",
      field: "periode_awal",
      type: "date",
      sortable: true,
    },
    {
      name: "Periode Akhir",
      field: "periode_akhir",
      type: "date",
      sortable: true,
    },
    { name: "No Izin", field: "no_izin" },
    { name: "Nilai Pajak", field: "nilai_pajak" },
    { name: "Keterangan", field: "note", className: "w-[300px]" },

    {
      name: "Izin Reklame",
      field: "file_izin_reklame",
      type: "custom",
      className: "text-center",
      render: (data) =>
        hasRoles("branch_ops|superadmin", auth) &&
        auth.permissions.includes("can add") ? (
          data.file_izin_reklame ? (
            <a
              className="text-blue-500 hover:underline"
              href={`/storage/ops/pajak-reklame/${data.file_izin_reklame}`}
              target="__blank"
            >
              {" "}
              {data.file_izin_reklame}
            </a>
          ) : (
            <Button
              variant="outlined"
              size="sm"
              color="blue"
              onClick={() => {
                toggleModalUpload();
                setFileType("file_izin_reklame");
                setData(data);
              }}
            >
              <div className="flex items-center gap-x-2">
                <ArrowUpTrayIcon className="w-4 h-4" />
                Upload Lampiran
              </div>
            </Button>
          )
        ) : (
          <span>Belum upload lampiran</span>
        ),
    },
    {
      name: "SKPD",
      field: "file_skpd",
      type: "custom",
      className: "text-center",
      render: (data) =>
        hasRoles("branch_ops|superadmin", auth) &&
        auth.permissions.includes("can add") ? (
          data.file_skpd ? (
            <a
              className="text-blue-500 hover:underline text-ellipsis"
              href={`/storage/ops/pajak-reklame/${data.file_skpd}`}
              target="__blank"
            >
              {" "}
              {data.file_skpd}
            </a>
          ) : (
            <Button
              variant="outlined"
              size="sm"
              color="blue"
              onClick={() => {
                toggleModalUpload();
                setFileType("file_skpd");
                setData(data);
              }}
            >
              <div className="flex items-center gap-x-2">
                <ArrowUpTrayIcon className="w-4 h-4" />
                Upload Lampiran
              </div>
            </Button>
          )
        ) : (
          <span>Belum upload lampiran</span>
        ),
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
    post(route("ops.pajak-reklame.import"), {
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
    window.open(
      route("ops.pajak-reklame.export") + `?branch=${branch}`,
      "_self"
    );
  };

  const handleSubmitUpload = (e) => {
    e.preventDefault();
    post(route("ops.pajak-reklame.upload", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalUploadOpen(!isModalUploadOpen);
      },
    });
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("ops.pajak-reklame.update", data.id), {
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
    post(route("ops.pajak-reklame.store", data.id), {
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
    destroy(route("ops.pajak-reklame.delete", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalDeleteOpen(!isModalDeleteOpen);
      },
    });
  };

  // const exportData = (e) => {
  //   e.preventDefault();
  //   const { branch, position } = data;
  //   const query =
  //     branch !== 0 && position !== 0
  //       ? `?branch=${branch}&position=${position}`
  //       : branch !== 0
  //       ? `?branch=${brancsh}`
  //       : position !== 0
  //       ? `?position=${position}`
  //       : "";

  //   window.open(route("pajak-reklame.export") + query, "_self");
  //   setData({ branch: 0, position: 0 });
  // };

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
      <Head title="OPS | Pajak Reklame Cabang" />
      <BreadcrumbsDefault />
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
            className="w-[1500px]"
            fetchUrl={"/api/ops/pajak-reklames"}
            refreshUrl={isRefreshed}
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
                label="Upload Lampiran (.pdf)"
                disabled={processing}
                type="file"
                name="upload"
                id="upload"
                accept=".pdf"
                onChange={(e) => setData(fileType, e.target.files[0])}
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
      <Modal
        isProcessing={processing}
        name="Create Report"
        isOpen={isModalExportOpen}
        onToggle={toggleModalExport}
        onSubmit={handleSubmitExport}
      >
        <div className="flex flex-col gap-y-4">
          <select
            label="Branch"
            disabled={processing}
            value={data.branch_id}
            onChange={(e) => setData("branch", e.target.value)}
          >
            <option value="0">All</option>
            {branches.map((branch) => (
              <option key={branch.id} value={`${branch.id}`}>
                {branch.branch_code} - {branch.branch_name}
              </option>
            ))}
          </select>
        </div>
      </Modal>
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
                label="Periode Awal"
                value={data.periode_awal || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("periode_awal", e.target.value)}
              />
              <Input
                label="Periode Akhir"
                value={data.periode_akhir || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("periode_akhir", e.target.value)}
              />
              <Input
                label="Keterangan"
                value={data.note || ""}
                disabled={processing}
                onChange={(e) => setData("note", e.target.value)}
              />
              <Input
                variant="standard"
                label="Upload File Izin Reklame"
                disabled={processing}
                type="file"
                name="file_izin_reklame"
                id="file_izin_reklame"
                accept=".xlsx"
                onChange={(e) =>
                  setData("file_izin_reklame", e.target.files[0])
                }
              />
              <Input
                variant="standard"
                label="Upload File SKPD"
                disabled={processing}
                type="file"
                name="file_skpd"
                id="file_skpd"
                accept=".xlsx"
                onChange={(e) => setData("file_skpd", e.target.files[0])}
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
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Select
                label="Branch"
                value={`${data.branch_id}`}
                disabled={processing}
                onChange={(e) => setData("branch_id", e)}
              >
                {branches.map((branch) => (
                  <Option key={branch.id} value={`${branch.id}`}>
                    {branch.branch_code} - {branch.branch_name}
                  </Option>
                ))}
              </Select>
              <Input
                label="Periode Awal"
                value={data.periode_awal || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("periode_awal", e.target.value)}
              />
              <Input
                label="Periode Akhir"
                value={data.periode_akhir || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("periode_akhir", e.target.value)}
              />
              <Input
                label="Keterangan"
                value={data.note || ""}
                disabled={processing}
                onChange={(e) => setData("note", e.target.value)}
              />
              <Input
                variant="standard"
                label="Upload File Izin Reklame"
                disabled={processing}
                type="file"
                name="file_izin_reklame"
                id="file_izin_reklame"
                accept=".xlsx"
                onChange={(e) =>
                  setData("file_izin_reklame", e.target.files[0])
                }
              />
              <Input
                variant="standard"
                label="Upload File SKPD"
                disabled={processing}
                type="file"
                name="file_skpd"
                id="file_skpd"
                accept=".xlsx"
                onChange={(e) => setData("file_skpd", e.target.files[0])}
              />
            </div>
          </DialogBody>
          <DialogFooter>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={processing} type="submit">
                Create
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
              {data.branches.branch_code} - {data.branches.branch_name}
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

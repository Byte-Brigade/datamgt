import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Reports/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentPlusIcon } from "@heroicons/react/24/outline";
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

export default function Page({ auth, branches, sessions, status_gedung, type_names }) {
  const initialData = {
    branch_id: 0,
    status_kepemilikan: null,
    jangka_waktu: null,
    open_date: null,
    jatuh_tempo: null,
    owner: null,
    biaya_per_tahun: 0,
    total_biaya: 0,
    branches: {
      branch_code: null,
      branch_name: null,
    },
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

  const columns = [
    { name: "Cabang", sortable: true, field: "branches.branch_name" },
    { name: "Tipe Cabang", field: "type_name", filterable: true },

    {
      name: "Status Kepemilikan",
      field: "status_kepemilikan",
      filterable: true,
      sortable: true,
    },
    {
      name: "Pemilik",
      field: "owner",
    },
    {
      name: "Masa Sewa",
      field: "jangka_waktu",
      className: "text-center",
    },
    {
      name: "Open Date",
      field: "open_date",

      type: "date",
      sortable: true,
      className: "justify-center text-center",
    },
    {
      name: "Jatuh Tempo / Sertifikat",
      field: "jatuh_tempo",
      type: "date",
      sortable: true,
      className: "justify-center text-center",
    },
    {
      name: "Biaya Per Tahun",
      field: "biaya_per_tahun",
      className: "text-right tabular-nums",
      type: "custom",
      render: (data) => {
        return data.biaya_per_tahun
          ? data.biaya_per_tahun.toLocaleString("id-ID")
          : "";
      },
    },
    {
      name: "Total Biaya",
      field: "total_biaya",
      className: "text-right tabular-nums",
      type: "custom",
      render: (data) => {
        return data.total_biaya ? data.total_biaya.toLocaleString("id-ID") : "";
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

  const handleSubmitImport = (e) => {
    e.preventDefault();
    post(route("infra.sewa_gedungs.import"), {
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
      route("infra.sewa_gedungs.export") + `?branch=${branch}`,
      "_self"
    );
    setIsModalExportOpen(!isModalExportOpen);
  };
  const handleSubmitUpload = (e) => {
    e.preventDefault();
    post(route("infra.sewa_gedungs.upload", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalUploadOpen(!isModalUploadOpen);
      },
    });
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("infra.sewa_gedungs.update", data.id), {
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
    post(route("infra.sewa_gedungs.store", data.id), {
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
    destroy(route("infra.sewa_gedungs.delete", data.id), {
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
      <Head title="GA | Sewa Gedung" />
      <BreadcrumbsDefault />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          {hasRoles("superadmin|admin|ga", auth) &&
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
                {auth.permissions.includes("can export") && (
                  <PrimaryButton onClick={toggleModalExport}>
                    Create Report
                  </PrimaryButton>
                )}
              </div>
            )}
          <DataTable
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("superadmin|admin|ga", auth) &&
                ["can edit", "can delete"].some((permission) =>
                  auth.permissions.includes(permission)
                )
                : true
            )}
            className="w-[1200px]"
            fetchUrl={"/api/infra/sewa-gedungs"}
            refreshUrl={isRefreshed}
            component={[
              {
                data: type_names,
                field: "type_name",
              },
              {
                data: status_gedung,
                field: "status_kepemilikan",
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
                className="file:border-0 file:text-sm file:font-medium file:text-white file:bg-slate-900 file:hover:opacity-90 file:cursor-pointer cursor-pointer file:rounded-lg file:py-2 file:px-3 !pt-2.5"
                containerProps={{
                  className: "h-fit",
                }}
              />
            </div>
          </DialogBody>
          <DialogFooter className="w-100 flex justify-between">
            <SecondaryButton type="button">
              <a href={route("infra.sewa_gedungs.template")}>
                Download Template
              </a>
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
                onChange={(e) => setData("file", e.target.files[0])}
                className="file:border-0 file:text-sm file:font-medium file:text-white file:bg-slate-900 file:hover:opacity-90 file:cursor-pointer cursor-pointer file:rounded-lg file:py-2 file:px-3 !pt-2.5"
                containerProps={{
                  className: "h-fit",
                }}
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
              <Select
                label="Status Kepemilikan"
                value={`${data.status_kepemilikan}`}
                disabled={processing}
                onChange={(e) => setData("status_kepemilikan", e)}
              >
                {status_gedung.map((status, index) => (
                  <Option key={index} value={`${status}`}>
                    {status}
                  </Option>
                ))}
              </Select>
              <Input
                label="Masa Sewa (Tahun)"
                value={data.jangka_waktu || ""}
                disabled={processing}
                type="text"
                onChange={(e) => setData("jangka_waktu", e.target.value)}
              />
              <Input
                label="Open Date"
                value={data.open_date || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("open_date", e.target.value)}
              />
              <Input
                label="Jatuh Tempo"
                value={data.jatuh_tempo || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("jatuh_tempo", e.target.value)}
              />
              <Input
                label="Owner"
                value={data.owner || ""}
                disabled={processing}
                onChange={(e) =>
                  setData("owner", e.target.value)
                }
              />
              <Input
                label="Biaya Per Tahun"
                value={data.biaya_per_tahun || ""}
                disabled={processing}
                type="number"
                onChange={(e) =>
                  setData("biaya_per_tahun", e.target.value)
                }
              />
              <Input
                label="Total Biaya"
                value={data.total_biaya || ""}
                disabled={processing}
                type="number"
                onChange={(e) =>
                  setData("total_biaya", e.target.value)
                }
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
          <DialogBody className="overflow-y-scroll " divider>
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
                label="Tanggal Pengesahan"
                value={data.tgl_pengesahan || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("tgl_pengesahan", e.target.value)}
              />
              <Input
                label="Tanggal Masa Berlaku"
                value={data.tgl_masa_berlaku || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("tgl_masa_berlaku", e.target.value)}
              />
              <Input
                label="Progress Resertifikasi"
                value={data.progress_resertifikasi || ""}
                disabled={processing}
                onChange={(e) =>
                  setData("progress_resertifikasi", e.target.value)
                }
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

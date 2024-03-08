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

export default function Detail({ auth, branches, sessions, scoring_vendor, status_pekerjaan }) {
  const initialData = {
    branch_id: 0,
    description: null,
    pic: null,
    status_pekerjaan: null,
    dokumen_perintah_kerja: null,
    vendor: null,
    nilai_project: null,
    tgl_selesai_pekerjaan: null,
    tgl_bast: null,
    tgl_request_scoring: null,
    tgl_scoring: null,
    sla: null,
    actual: null,
    scoring_vendor: null,
    schedule_scoring: null,
    keterangan: null,

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
    { name: "Cabang", field: "branches.branch_name", sortable: true },

    {
      name: "Description",
      field: "description",
    },
    {
      name: "PIC",
      field: "pic",
    },

    {
      name: "Status Pekerjaan",
      field: "status_pekerjaan",
      filterable: true,
    },
    {
      name: "Dokumen Perintah Kerja",
      field: "dokumen_perintah_kerja",
    },
    {
      name: "Vendor",
      field: "vendor",
    },
    {
      name: "Nilai Project",
      field: "nilai_project",
      className: "text-right tabular-nums",
      type: "custom",
      render: (data) => {
        return data.nilai_project
          ? data.nilai_project.toLocaleString("id-ID")
          : 0;
      },
    },
    {
      name: "Tanggal Selesai Pekerjaan",
      field: "tgl_selesai_pekerjaan",
      type: "date",
      sortable: true,
      className: "justify-center text-center w-[100px]",
    },
    {
      name: "Tanggal BAST",
      field: "tgl_bast",
      type: "date",
      sortable: true,
      className: "justify-center text-center w-[100px]",
    },
    {
      name: "Tanggal Scoring",
      field: "tgl_scoring",
      type: "date",
      sortable: true,
      className: "justify-center text-center w-[100px]",
    },
    {
      name: "SLA",
      field: "sla",
    },
    {
      name: "Actual",
      field: "actual",
    },
    {
      name: "Meet The SLA",
      field: "meet_the_sla",
    },
    {
      name: "Scoring Vendor",
      field: "scoring_vendor",
    },
    {
      name: "Schedule Scoring",
      field: "schedule_scoring",
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
            console.log(data);
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
    post(route("infra.scoring-projects.import"), {
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
      route("infra.scoring-projects.export") + `?branch=${branch}`,
      "_self"
    );
    setIsModalExportOpen(!isModalExportOpen);
  };
  const handleSubmitUpload = (e) => {
    e.preventDefault();
    post(route("infra.scoring-projects.upload", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalUploadOpen(!isModalUploadOpen);
      },
    });
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("infra.scoring-projects.update", data.id), {
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
    post(route("infra.scoring-projects.store", data.id), {
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
    destroy(route("infra.scoring-projects.delete", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalDeleteOpen(!isModalDeleteOpen);
      },
    });
  };

  const calculateDifference = (startDate, endDate) => {
    let days = 0;
    const tgl_bast = new Date(startDate);
    const tgl_scoring = new Date(endDate);
    if (isNaN(tgl_bast) || isNaN(tgl_bast)) {
      console.log("tgl tidak valid");
      return;
    }
    const difference = Math.abs(tgl_scoring - tgl_bast); // Menggunakan nilai mutlak
    days = difference / (1000 * 60 * 60 * 24); // Convert milliseconds to days
    console.log(days);
    setData({
      ...data,
      tgl_bast: startDate,
      tgl_scoring: endDate,
      actual: days,
    });
  };

  const handleTglBast = (e) => {
    setData("tgl_bast", e.target.value);
    calculateDifference(e.target.value, data.tgl_scoring);
    console.log(e.target.value);
  };
  const handleTglScoring = (e) => {
    setData("tgl_scoring", e.target.value);
    calculateDifference(data.tgl_bast, e.target.value);
    console.log(e.target.value);
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
    setData(initialData);
    setIsModalCreateOpen(!isModalCreateOpen);
  };

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          {hasRoles("superadmin|admin|ga", auth) &&
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
                {auth.permissions.includes("can export") &
                  (
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
            className="w-[1500px]"
            fetchUrl={`/api/infra/scoring-projects/${scoring_vendor}`}
            refreshUrl={isRefreshed}
            bordered={true}
            component={[
              {
                data: Object.values(status_pekerjaan),
                field: 'status_pekerjaan'
              }
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
                onChange={(e) => setData("file", e.target.files[0])}
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
          <DialogBody divider className="overflow-y-auto max-h-96">
            <div className="flex flex-col gap-y-4">
              <Select
                label="Branch"
                value={`${data.branch_id}`}
                disabled={processing}
                onChange={(e) => setData("branch_id", e)}
              >
                {branches.map((branch) =>
                  branch.branch_name.includes("Pusat") ? (
                    <Option value={`${branch.id}`}>{branch.branch_name}</Option>
                  ) : (
                    <Option key={branch.id} value={`${branch.id}`}>
                      {branch.branch_code} - {branch.branch_name}
                    </Option>
                  )
                )}
              </Select>

              <Input
                label="Deskripsi"
                value={data.description || ""}
                disabled={processing}
                onChange={(e) => setData("description", e.target.value)}
              />
              <Input
                label="PIC"
                value={data.pic || ""}
                disabled={processing}
                onChange={(e) => setData("pic", e.target.value)}
              />
              <Select
                label="Status Pekerjaan"
                value={`${data.status_pekerjaan}`}
                disabled={processing}
                onChange={(e) => setData("status_pekerjaan", e)}
              >
                <Option value="Done">Done</Option>
                <Option value="On Progress">On Progress</Option>
              </Select>
              <Input
                label="Dokumen Perintah Kerja"
                value={data.dokumen_perintah_kerja || ""}
                disabled={processing}
                onChange={(e) =>
                  setData("dokumen_perintah_kerja", e.target.value)
                }
              />
              <Input
                label="Vendor"
                value={data.vendor || ""}
                disabled={processing}
                onChange={(e) => setData("vendor", e.target.value)}
              />
              <Input
                label="Nilai Project"
                type="number"
                value={data.nilai_project || ""}
                disabled={processing}
                onChange={(e) => setData("nilai_project", e.target.value)}
              />
              <Input
                label="Tanggal Selesai Pekerjaan"
                value={data.tgl_selesai_pekerjaan || ""}
                disabled={processing}
                type="date"
                onChange={(e) =>
                  setData("tgl_selesai_pekerjaan", e.target.value)
                }
              />
              <Input
                label="Tanggal BAST"
                value={data.tgl_bast || ""}
                disabled={processing}
                type="date"
                onChange={handleTglBast}
              />
              <Input
                label="Tanggal Request Scoring"
                value={data.tgl_request_scoring || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("tgl_request_scoring", e.target.value)}
              />
              <Input
                label="Tanggal Scoring"
                value={data.tgl_scoring || ""}
                disabled={processing}
                type="date"
                onChange={handleTglScoring}
              />
              <Input
                label="SLA"
                type="number"
                value={data.sla || ""}
                disabled={processing}
                onChange={(e) => setData("sla", e.target.value)}
              />
              <Input
                label="Actual"
                value={data.actual || ""}
                disabled={processing}
                onChange={(e) => setData("actual", e.target.value)}
              />
              <Input
                label="Scoring Vendor"
                value={data.scoring_vendor || ""}
                disabled={processing}
                onChange={(e) => setData("scoring_vendor", e.target.value)}
              />
              <Select
                label="Schedule Scoring"
                value={`${data.schedule_scoring}`}
                disabled={processing}
                onChange={(e) => setData("schedule_scoring", e)}
              >
                <Option value="Q1">Q1</Option>
                <Option value="Q2">Q2</Option>
                <Option value="Q3">Q3</Option>
                <Option value="Q4">Q4</Option>
              </Select>
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
          <DialogBody divider className="overflow-y-auto max-h-96">
            <div className="flex flex-col gap-y-4">
              <Select
                label="Branch"
                value={`${data.branch_id}`}
                disabled={processing}
                onChange={(e) => setData("branch_id", e)}
              >
                {branches.map((branch) =>
                  branch.branch_name.includes("Pusat") ? (
                    <Option key={branch.id} value={`${branch.id}`}>
                      {branch.branch_name}
                    </Option>
                  ) : (
                    <Option key={branch.id} value={`${branch.id}`}>
                      {branch.branch_code} - {branch.branch_name}
                    </Option>
                  )
                )}
              </Select>

              <Input
                label="Deskripsi"
                value={data.description || ""}
                disabled={processing}
                onChange={(e) => setData("description", e.target.value)}
              />
              <Input
                label="PIC"
                value={data.pic || ""}
                disabled={processing}
                onChange={(e) => setData("pic", e.target.value)}
              />
              <Select
                label="Status Pekerjaan"
                value={`${data.status_pekerjaan}`}
                disabled={processing}
                onChange={(e) => setData("status_pekerjaan", e)}
              >
                <Option value="Done">Done</Option>
                <Option value="On Progress">On Progress</Option>
              </Select>
              <Input
                label="Dokumen Perintah Kerja"
                value={data.dokumen_perintah_kerja || ""}
                disabled={processing}
                onChange={(e) =>
                  setData("dokumen_perintah_kerja", e.target.value)
                }
              />
              <Input
                label="Vendor"
                value={data.vendor || ""}
                disabled={processing}
                onChange={(e) => setData("vendor", e.target.value)}
              />
              <Input
                label="Nilai Project"
                type="number"
                value={data.nilai_project || ""}
                disabled={processing}
                onChange={(e) => setData("nilai_project", e.target.value)}
              />
              <Input
                label="Tanggal Selesai Pekerjaan"
                value={data.tgl_selesai_pekerjaan || ""}
                disabled={processing}
                type="date"
                onChange={(e) =>
                  setData("tgl_selesai_pekerjaan", e.target.value)
                }
              />
              <Input
                label="Tanggal BAST"
                value={data.tgl_bast || ""}
                disabled={processing}
                type="date"
                onChange={handleTglBast}
              />
              <Input
                label="Tanggal Request Scoring"
                value={data.tgl_request_scoring || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("tgl_request_scoring", e.target.value)}
              />
              <Input
                label="Tanggal Scoring"
                value={data.tgl_scoring || ""}
                disabled={processing}
                type="date"
                onChange={handleTglScoring}
              />
              <Input
                label="SLA"
                type="number"
                value={data.sla || ""}
                disabled={processing}
                onChange={(e) => setData("sla", e.target.value)}
              />
              <Input
                label="Actual"
                value={data.actual || ""}
                type="number"
                disabled={processing}
                onChange={(e) => setData("actual", e.target.value)}
              />
              <Input
                label="Scoring Vendor"
                value={data.scoring_vendor || ""}
                disabled={processing}
                onChange={(e) => setData("scoring_vendor", e.target.value)}
              />
              <Select
                label="Schedule Scoring"
                value={`${data.schedule_scoring}`}
                disabled={processing}
                onChange={(e) => setData("schedule_scoring", e)}
              >
                <Option value="Q1">Q1</Option>
                <Option value="Q2">Q2</Option>
                <Option value="Q3">Q3</Option>
                <Option value="Q4">Q4</Option>
              </Select>
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

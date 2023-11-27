import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Reports/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { ArrowUpTrayIcon, DocumentPlusIcon } from "@heroicons/react/24/outline";
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

export default function Page({ auth, branches, sessions }) {
  const initialData = {
    branch_id: 0,
    category: null,
    asset_number: null,
    asset_description: null,
    date_in_place_service: null,
    asset_cost: null,
    asset_location: null,
    major_category: null,
    minor_category: null,
    depre_exp: null,
    net_book_value: null,

    branches: {
      branch_code: null,
      branch_name: null,
    },
    expired_date: null,
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
      name: "Category",
      field: "category",
      className: "text-center",
      sortable: true,
    },
    {
      name: "Asset Number",
      field: "asset_number",
      className: "text-center",
      sortable: true,
    },
    {
      name: "Date In Place",
      field: "date_in_place_service",
      type: "date",
      sortable: true,
      className: "justify-center text-center",
    },
    {
      name: "Asset Description",
      field: "asset_description",
      className: "text-center",
    },
    {
      name: "Asset Location",
      field: "asset_location",
      className: "text-center",
    },
    {
      name: "Net Book Value",
      field: "net_book_value",
      className: "text-center",
      sortable: true,
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
      name: "Depre Exp",
      field: "depre_exp",
      className: "text-center",
      sortable: true,
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
    post(route("gap.assets.import"), {
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
    window.open(route("gap.assets.export") + `?branch=${branch}`, "_self");
    setIsModalExportOpen(!isModalExportOpen);
  };
  const handleSubmitUpload = (e) => {
    e.preventDefault();
    post(route("gap.assets.upload", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalUploadOpen(!isModalUploadOpen);
      },
    });
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("gap.assets.update", data.id), {
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
    post(route("gap.assets.store", data.id), {
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
    destroy(route("gap.assets.delete", data.id), {
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
      <Head title="GA Procurement | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex items-center justify-between mb-4">
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
            <PrimaryButton onClick={toggleModalExport}>
              Create Report
            </PrimaryButton>
          </div>
          <DataTable
            columns={columns}
            fetchUrl={"/api/gap/assets"}
            refreshUrl={isRefreshed}
            bordered={true}
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
          <DialogBody className="overflow-y-auto max-h-96" divider>
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
                label="Branch"
                value={`${data.category}`}
                disabled={processing}
                onChange={(e) => setData("category", e)}
              >
                <Option value="Depre">Depre</Option>
                <Option value="Non-Depre">Non-Depre</Option>
              </Select>
              <Input
                label="Asset Number"
                type="number"
                value={data.asset_number || ""}
                disabled={processing}
                onChange={(e) => setData("asset_number", e.target.value)}
              />
              <Input
                label="Asset Description"
                value={data.asset_description || ""}
                disabled={processing}
                onChange={(e) => setData("asset_description", e.target.value)}
              />
              <Input
                label="Asset Cost"
                type="number"
                value={data.asset_cost || ""}
                disabled={processing}
                onChange={(e) => setData("asset_cost", e.target.value)}
              />
              <Input
                label="Asset Location"
                value={data.asset_location || ""}
                disabled={processing}
                onChange={(e) => setData("asset_location", e.target.value)}
              />
              <Input
                label="Date In Place Service"
                value={data.date_in_place_service || ""}
                disabled={processing}
                type="date"
                onChange={(e) =>
                  setData("date_in_place_service", e.target.value)
                }
              />
              <Input
                label="Major Category"
                value={data.major_category || ""}
                disabled={processing}
                onChange={(e) => setData("major_category", e.target.value)}
              />
              <Input
                label="Minor Category"
                value={data.minor_category || ""}
                disabled={processing}
                onChange={(e) => setData("minor_category", e.target.value)}
              />
              <Input
                label="Depre Exp"
                type="number"
                step="0.01"
                value={data.depre_exp || ""}
                disabled={processing}
                onChange={(e) => setData("depre_exp", e.target.value)}
              />
              <Input
                label="Net Book Value"
                type="number"
                value={data.net_book_value || ""}
                disabled={processing}
                onChange={(e) => setData("net_book_value", e.target.value)}
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
          <DialogBody className="overflow-y-auto max-h-96" divider>
            <div className="flex flex-col gap-y-4">
              <Select
                label="Branch"
                value={`${data.branch_id}`}
                disabled={processing}
                onChange={(e) => setData("branch_id", e)}
              >
                {branches.map((branch) =>
                  branch.branch_name.includes("Sampoerna") ? (
                    <Option key={branch.id} value={`${branch.id}`}>
                      {branch.branch_code} - {branch.branch_name} (HO)
                    </Option>
                  ) : (
                    <Option key={branch.id} value={`${branch.id}`}>
                      {branch.branch_code} - {branch.branch_name}
                    </Option>
                  )
                )}
              </Select>
              <Select
                label="Category"
                value={`${data.category}`}
                disabled={processing}
                onChange={(e) => setData("category", e)}
              >
                <Option value="Depre">Depre</Option>
                <Option value="Non-Depre">Non-Depre</Option>
              </Select>
              <Input
                label="Asset Number"
                type="number"
                value={data.asset_number || ""}
                disabled={processing}
                onChange={(e) => setData("asset_number", e.target.value)}
              />
              <Input
                label="Asset Description"
                value={data.asset_description || ""}
                disabled={processing}
                onChange={(e) => setData("asset_description", e.target.value)}
              />
              <Input
                label="Asset Cost"
                type="number"
                value={data.asset_cost || ""}
                disabled={processing}
                onChange={(e) => setData("asset_cost", e.target.value)}
              />
              <Input
                label="Asset Location"
                value={data.asset_location || ""}
                disabled={processing}
                onChange={(e) => setData("asset_location", e.target.value)}
              />
              <Input
                label="Date In Place Service"
                value={data.date_in_place_service || ""}
                disabled={processing}
                type="date"
                onChange={(e) =>
                  setData("date_in_place_service", e.target.value)
                }
              />
              <Input
                label="Major Category"
                value={data.major_category || ""}
                disabled={processing}
                onChange={(e) => setData("major_category", e.target.value)}
              />
              <Input
                label="Minor Category"
                value={data.minor_category || ""}
                disabled={processing}
                onChange={(e) => setData("minor_category", e.target.value)}
              />
              <Input
                label="Depre Exp"
                type="number"
                step="0.01"
                value={data.depre_exp || ""}
                disabled={processing}
                onChange={(e) => setData("depre_exp", e.target.value)}
              />
              <Input
                label="Net Book Value"
                type="number"
                value={data.net_book_value || ""}
                disabled={processing}
                onChange={(e) => setData("net_book_value", e.target.value)}
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

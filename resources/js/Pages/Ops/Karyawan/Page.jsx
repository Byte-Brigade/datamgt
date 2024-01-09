import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentArrowDownIcon, DocumentPlusIcon, ArrowPathIcon } from "@heroicons/react/24/outline";
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

export default function Karyawan({ auth, branches, positions, sessions, employees }) {
  const initialData = {
    file: null,
    branch: "0",
    position: "0",
    employee_id: null,
    name: null,
    email: null,
    branches: {
      id: null,
    },
    employee_positions: {
      id: null,
    },
    gender: null,
    birth_date: null,
    hiring_date: null,
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
  console.log(employees)
  const columns = [
    { name: "Nama Cabang", field: "branches.branch_name", sortable: true },
    {
      name: "Posisi",
      field: "employee_positions.position_name",
      sortable: true,
      filterable: true,
    },
    { name: "NIK", field: "employee_id", sortable: true },
    {
      name: "Nama Lengkap",
      field: "name",
      sortable: true,
      className: "w-[300px]",
    },
    { name: "Email (@banksampoerna.com)", field: "email" },
    {
      name: "Tanggal Lahir",
      field: "birth_date",
      type: "date",
      className: "text-center w-[300px]",
    },
    {
      name: "Hiring Date",
      field: "hiring_date",
      type: "date",
      className: "text-center w-[300px]",
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
    post(route("ops.employees.import"), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalImportOpen(!isModalImportOpen);
      },
    });
  };
  const handleSubmitSync = (e) => {
    e.preventDefault();
    post(route("ops.employees.sync"), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
      },
    });
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("ops.employees.update", data.id), {
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
    post(route("ops.employees.store", data.id), {
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
    destroy(route("ops.employees.delete", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalDeleteOpen(!isModalDeleteOpen);
      },
    });
  };

  const handleSubmitExport = (e) => {
    e.preventDefault();
    const { branch, position } = data;
    const query =
      branch !== "0" && position !== "0"
        ? `?branch=${branch}&position=${position}`
        : branch !== "0"
        ? `?branch=${branch}`
        : position !== "0"
        ? `?position=${position}`
        : "";

    window.open(route("ops.employees.export") + query, "__blank");
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
    setData(initialData);
    setIsModalCreateOpen(!isModalCreateOpen);
  };

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  const onInputNumber = (e) => {
    e.target.value = e.target.value
      .replace(/[^0-9.]/g, "")
      .replace(/(\..*)\./g, "$1");
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <BreadcrumbsDefault />
      <Head title="Karyawan Bank OPS Cabang" />
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
                      onClick={handleSubmitSync}
                    >
                      <div className="flex items-center gap-x-2">
                        <ArrowPathIcon className="w-4 h-4" />
                        Sync
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
            fetchUrl={"/api/ops/employees"}
            refreshUrl={isRefreshed}
            component={[
              {
                data: Array.from(
                  new Set(positions.map((position) => position.position_name))
                ),
                field: "employee_positions.position_name",
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
          <DialogFooter className="w-100 flex justify-between">
            <SecondaryButton type="button">
              <a href={route("ops.employees.template")}>Download Template</a>
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
      {/* Modal Export */}
      <Dialog open={isModalExportOpen} handler={toggleModalExport} size="md">
        <DialogHeader className="flex items-center justify-between">
          Export Data
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
        <form onSubmit={handleSubmitExport} encType="multipart/form-data">
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Select
                label="Branch"
                disabled={processing}
                value={data.branch}
                onChange={(e) => setData("branch", e)}
              >
                <Option value="0">All</Option>
                {branches.map((branch) => (
                  <Option key={branch.id} value={`${branch.id}`}>
                    {branch.branch_code} - {branch.branch_name}
                  </Option>
                ))}
              </Select>
              <Select
                label="Position"
                value={data.position}
                disabled={processing}
                onChange={(e) => setData("position", e)}
              >
                <Option value="0">All</Option>
                {positions.map((position) => (
                  <Option key={position.id} value={`${position.id}`}>
                    {position.position_name}
                  </Option>
                ))}
              </Select>
            </div>
          </DialogBody>
          <DialogFooter>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={processing} type="submit">
                Simpan
              </Button>
              <SecondaryButton type="button" onClick={toggleModalExport}>
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
                label="Employee ID"
                value={data.employee_id}
                disabled={processing}
                maxLength={8}
                onInput={(e) => onInputNumber(e)}
                onChange={(e) => setData("employee_id", e.target.value)}
              />
              <Input
                label="Employee Name"
                value={data.name}
                disabled={processing}
                onChange={(e) => setData("name", e.target.value)}
              />
              <Input
                label="Email"
                value={data.email}
                disabled={processing}
                onChange={(e) => setData("email", e.target.value)}
              />
              <Select
                label="Branch"
                value={`${data.branch}`}
                disabled={processing}
                onChange={(e) => setData("branch", e)}
              >
                {branches.map((branch) => (
                  <Option key={branch.id} value={`${branch.id}`}>
                    {branch.branch_code} - {branch.branch_name}
                  </Option>
                ))}
              </Select>
              <Select
                label="Position"
                value={`${data.position}`}
                disabled={processing}
                onChange={(e) => setData("position", e)}
              >
                {positions.map((position) => (
                  <Option key={position.id} value={`${position.id}`}>
                    {position.position_name}
                  </Option>
                ))}
              </Select>
              <Input
                label="Tanggal Lahir"
                value={data.birth_date || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("birth_date", e.target.value)}
              />
              <Input
                label="Hiring Date"
                value={data.hiring_date || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("hiring_date", e.target.value)}
              />
              <div className="flex gap-x-4">
                <Radio
                  name="gender"
                  label="Laki-laki"
                  color="blue"
                  checked={data.gender === "L"}
                  value="L"
                  onChange={(e) => setData("gender", e.target.value)}
                />
                <Radio
                  name="gender"
                  label="Perempuan"
                  color="pink"
                  checked={data.gender === "P"}
                  value="P"
                  onChange={(e) => setData("gender", e.target.value)}
                />
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
              <Input
                label="Employee ID"
                value={data.employee_id}
                disabled={processing}
                maxLength={8}
                onInput={(e) => onInputNumber(e)}
                onChange={(e) => setData("employee_id", e.target.value)}
              />
              <Input
                label="Employee Name"
                value={data.name}
                disabled={processing}
                onChange={(e) => setData("name", e.target.value)}
              />
              <Input
                label="Email"
                value={data.email}
                disabled={processing}
                onChange={(e) => setData("email", e.target.value)}
              />
              <Select
                label="Branch"
                value={`${data.branch}`}
                disabled={processing}
                onChange={(e) => setData("branch", e)}
              >
                {branches.map((branch) => (
                  <Option key={branch.id} value={`${branch.id}`}>
                    {branch.branch_code} - {branch.branch_name}
                  </Option>
                ))}
              </Select>
              <Select
                label="Position"
                value={`${data.position}`}
                disabled={processing}
                onChange={(e) => setData("position", e)}
              >
                {positions.map((position) => (
                  <Option key={position.id} value={`${position.id}`}>
                    {position.position_name}
                  </Option>
                ))}
              </Select>
              <Input
                label="Tanggal Lahir"
                value={data.birth_date || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("birth_date", e.target.value)}
              />
              <Input
                label="Hiring Date"
                value={data.hiring_date || ""}
                disabled={processing}
                type="date"
                onChange={(e) => setData("hiring_date", e.target.value)}
              />
              <div className="flex gap-x-4">
                <Radio
                  name="gender"
                  label="Laki-laki"
                  color="blue"
                  checked={data.gender === "L"}
                  value="L"
                  onChange={(e) => setData("gender", e.target.value)}
                />
                <Radio
                  name="gender"
                  label="Perempuan"
                  color="pink"
                  checked={data.gender === "P"}
                  value="P"
                  onChange={(e) => setData("gender", e.target.value)}
                />
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
              {data.employee_id} - {data.name}
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

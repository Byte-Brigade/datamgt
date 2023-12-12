import Alert from "@/Components/Alert";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { DocumentPlusIcon } from "@heroicons/react/24/outline";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head, useForm } from "@inertiajs/react";

import {
  Button,
  Checkbox,
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

export default function UAM({ positions, sessions, permissions, auth }) {
  const initialData = {
    name: null,
    nik: null,
    position: null,
    entity: null,
    permissions: ["can view"],
    password: null,
    password_confirmation: null,
  };
  const {
    data,
    setData,
    post,
    put,
    delete: destroy,
    processing,
    errors,
    register,
  } = useForm(initialData);

  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isModalEditOpen, setIsModalEditOpen] = useState(false);
  const [isModalCreateOpen, setIsModalCreateOpen] = useState(false);
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);
  const [fileType, setFileType] = useState("file");
  const columns = [
    // { name: "Branch ID", field: "branches.branch_code", sortable: true },
    // { name: "Branch Name", field: "branches.branch_name", sortable: true },
    {
      name: "Nama",
      field: "name",
      sortable: true,
    },
    {
      name: "NIK",
      field: "nik",
      sortable: true,
    },
    { name: "Posisi", field: "position" },

    {
      name: "View",
      field: "permissions",
      type: "custom",
      key: "view",
      render: (data) =>
        data.permissions
          .filter((permission) => permission == "can view")
          .join(""),
    },
    {
      name: "Edit",
      field: "permissions",
      type: "custom",
      key: "edit",
      render: (data) =>
        data.permissions
          .filter((permission) => permission == "can edit")
          .join(""),
    },
    {
      name: "Delete",
      field: "permissions",
      type: "custom",
      key: "delete",
      render: (data) =>
        data.permissions
          .filter((permission) => permission == "can delete")
          .join(""),
    },
    {
      name: "Add",
      field: "permissions",
      type: "custom",
      key: "add",
      render: (data) =>
        data.permissions
          .filter((permission) => permission == "can add")
          .join(""),
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

  const handleSubmitExport = (e) => {
    const { branch } = data;
    e.preventDefault();
    window.open(route("uam.export") + `?branch=${branch}`, "_self");
  };

  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("uam.update", data.id), {
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
    post(route("uam.store"), {
      method: "post",
      replace: true,
      onSuccess: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalCreateOpen(!isModalCreateOpen);
        setData(initialData);
      },
      onError: (errors) => {
        // Handle validation errors
        setData("errors", errors);
      },
    });
  };

  const handleSubmitDelete = (e) => {
    e.preventDefault();
    destroy(route("uam.delete", data.id), {
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

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  const toggleModalCreate = () => {
    setIsModalCreateOpen(!isModalCreateOpen);
    !isModalCreateOpen && setData(initialData);
  };
  const toggleModalEdit = () => {
    setIsModalEditOpen(!isModalEditOpen);
  };

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  console.log(data);

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="User Access Management" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex items-center justify-between mb-4">
            <div className="flex mr-[1em]">
              <PrimaryButton
                className="bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
                onClick={toggleModalCreate}
              >
                <div className="flex items-center gap-x-2">
                  <DocumentPlusIcon className="w-4 h-4" />
                  Add User
                </div>
              </PrimaryButton>
            </div>
            {/* <PrimaryButton onClick={toggleModalExport}>
              Create Report
            </PrimaryButton> */}
          </div>
          <DataTable
            columns={columns}
            fetchUrl={"/api/uam"}
            refreshUrl={isRefreshed}
          />
        </div>
      </div>
      {/* Modal Create */}
      <Dialog open={isModalCreateOpen} handler={toggleModalCreate} size="md">
        <DialogHeader className="flex items-center justify-between">
          Add User
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
                label="Nama"
                value={data.name}
                disabled={processing}
                onChange={(e) => setData("name", e.target.value)}
              />
              <Input
                label="NIK"
                value={data.nik}
                disabled={processing}
                onChange={(e) => setData("nik", e.target.value)}
                maxLength={8}
              />
              <Select
                label="Posisi"
                value={`${data.position || ""}`}
                disabled={processing}
                onChange={(e) => setData("position", e)}
              >
                {positions.map((position) => (
                  <Option key={position.id} value={`${position.id}`}>
                    {position.alt_name}
                  </Option>
                ))}
              </Select>
              <Input
                v-model="password"
                type="password"
                label="Password"
                value={data.password}
                disabled={processing}
                onChange={(e) => setData("password", e.target.value)}
              />
              <Input
                v-model="password_confirmation"
                type="password"
                label="Confirm Password"
                value={data.password_confirmation}
                disabled={processing}
                onChange={(e) =>
                  setData("password_confirmation", e.target.value)
                }
              />
              {errors.password && (
                <div className="text-red-700 error">{errors.password}</div>
              )}
              <div className="flex flex-col">
                <span className="text-sm font-light">Hak Akses</span>
                <div className="flex gap-x-4">
                  {permissions.map((permission, index) => (
                    <Checkbox
                      key={index}
                      label={permission.name}
                      checked={data.permissions.includes(permission.name)}
                      onChange={(e) =>
                        setData(
                          "permissions",
                          data.permissions.includes(permission.name)
                            ? data.permissions.filter(
                                (p) => p != e.target.value
                              )
                            : [...data.permissions, e.target.value]
                        )
                      }
                      value={permission.name}
                    />
                  ))}
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
                label="Nama"
                value={data.name}
                disabled={processing}
                onChange={(e) => setData("name", e.target.value)}
              />
              <Input
                label="NIK"
                value={data.nik}
                disabled={processing}
                onChange={(e) => setData("nik", e.target.value)}
              />
              <Select
                label="Posisi"
                value={`${data.position || ""}`}
                disabled={processing}
                onChange={(e) => setData("position", e)}
              >
                {positions.map((position) => (
                  <Option
                    key={position.alt_name}
                    value={`${position.alt_name}`}
                  >
                    {position.alt_name}
                  </Option>
                ))}
              </Select>
              {/* <Input
                type="password"
                label="Password"
                value={data.password}
                disabled={processing}
                onChange={(e) => setData("password", e.target.value)}
              />
              <Input
                type="password"
                label="Confirm Password"
                value={data.password_confirmation}
                disabled={processing}
                onChange={(e) => setData("password_confirmation", e.target.value)}
              /> */}
              <div className="flex flex-col">
                <span className="text-sm font-light">Hak Akses</span>
                <div className="flex gap-x-4">
                  {permissions.map((permission, index) => (
                    <Checkbox
                      key={index}
                      label={permission.name}
                      checked={data.permissions.includes(permission.name)}
                      onChange={(e) =>
                        setData(
                          "permissions",
                          data.permissions.includes(permission.name)
                            ? data.permissions.filter(
                                (p) => p != e.target.value
                              )
                            : [...data.permissions, e.target.value]
                        )
                      }
                      value={permission.name}
                    />
                  ))}
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
              {data.nik} - {data.name}
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

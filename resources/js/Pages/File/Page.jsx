import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { XMarkIcon } from "@heroicons/react/24/outline";
import { Head, useForm, usePage } from "@inertiajs/react";
import { Button, Dialog, DialogBody, DialogFooter, DialogHeader, IconButton, Typography } from "@material-tailwind/react";
import { useState } from "react";

export default function Branch({ sessions, auth }) {
  const { url } = usePage();
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const initialData = {
    id: null,
    table_name: null,
    filename: null,
    path: null,
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
  const columns = [
    {
      name: "Table",
      field: "table_name",
    },
    {
      name: "Filename",
      field: "filename",
    },
    {
      name: "Path",
      field: "path",
    },
    {
      name: "Uploaded By",
      field: "uploaded_by",
    },
    {
      name: "Status",
      field: "status",
    },
    {
      name: "Download",
      field: "detail",
      className: "text-center",
      render: (data) => (
        <a
          href={route("files.download", data.id)}
        >
          <Button variant="outlined">Download</Button>
        </a>
      ),
    },
    {
      name: "Action",
      field: "action",
      className: "text-center",
      render: (data) => (
        <DropdownMenu
          placement="left-start"
          onDeleteClick={() => {
            toggleModalDelete();
            setData(data);
          }}
        />
      ),
    },
  ];

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  const handleSubmitDelete = (e) => {
    e.preventDefault();
    destroy(route("files.delete", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalDeleteOpen(!isModalDeleteOpen);
      },
    });
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Branch" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <DataTable

            fetchUrl={"/api/report/files"}
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("superadmin|admin|procurement", auth) &&
                ["can edit", "can delete"].some((permission) =>
                  auth.permissions.includes(permission)
                )
                : true
            )}
          />
        </div>
      </div>
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
              {data.filename}
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

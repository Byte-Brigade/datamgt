import Alert from "@/Components/Alert";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
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

export default function Detail({ auth, sessions, kdo_mobil }) {
  console.log(kdo_mobil);
  const initialData = {
    titik_posisi: null,
    expired_date: null,
    id: null,
  };

  const {
    data,
    setData,
    put,
    delete: destroy,
    processing,
    errors,
  } = useForm(initialData);

  const [isModalEditOpen, setIsModalEditOpen] = useState(false);
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);

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
    destroy(route("gap.kdo.mobil.delete", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalDeleteOpen(!isModalDeleteOpen);
      },
    });
  };

  const toggleModalEdit = () => {
    setIsModalEditOpen(!isModalEditOpen);
  };

  const toggleModalDelete = () => {
    setIsModalDeleteOpen(!isModalDeleteOpen);
  };

  const columns = [
    { name: "Vendor", field: "vendor", sortable: true },
    { name: "Cabang", field: "branches.branch_name", sortable: true },
    { name: "Nopol", field: "nopol", className: "w-[300px]" },
    {
      name: "Awal Sewa",
      type: "date",
      field: "awal_sewa",
      sortable: true,
      className: "w-[300px]",
    },
    {
      name: "Akhir Sewa",
      type: "date",
      field: "akhir_sewa",
      sortable: true,
      className: "w-[300px]",
    },
    {
      name: "January 2023",
      field: "biaya_sewa.january",
      className: "text-center w-[300px]",
    },
    {
      name: "February 2023",
      field: "biaya_sewa.february",
      className: "text-center w-[300px]",
    },
    {
      name: "March 2023",
      field: "biaya_sewa.march",
      className: "text-center w-[300px]",
    },
    {
      name: "April 2023",
      field: "biaya_sewa.april",
      className: "text-center w-[300px]",
    },
    {
      name: "May 2023",
      field: "biaya_sewa.may",
      className: "text-center w-[300px]",
    },
    {
      name: "June 2023",
      field: "biaya_sewa.june",
      className: "text-center w-[300px]",
    },
    {
      name: "July 2023",
      field: "biaya_sewa.july",
      className: "text-center w-[300px]",
    },
    {
      name: "August 2023",
      field: "biaya_sewa.august",
      className: "text-center w-[300px]",
    },
    {
      name: "September 2023",
      field: "biaya_sewa.september",
      className: "text-center w-[300px]",
    },
    {
      name: "October 2023",
      field: "biaya_sewa.october",
      className: "text-center w-[300px]",
    },
    {
      name: "November 2023",
      field: "biaya_sewa.november",
      className: "text-center w-[300px]",
    },
    {
      name: "December 2023",
      field: "biaya_sewa.december",
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

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={`GA Procurement | KDO Mobil`} />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <h2 className="mb-4 text-xl font-semibold text-center">
            {kdo_mobil.branches.branch_name}
          </h2>
          <DataTable
            columns={columns}
            fetchUrl={`/api/gap/kdo/mobil/${kdo_mobil.id}`}
            refreshUrl={isRefreshed}
            className="w-[2000px]"
          />
        </div>
      </div>
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
                label="Titik Posisi"
                value={data.titik_posisi || ""}
                disabled={processing}
                onChange={(e) => setData("titik_posisi", e.target.value)}
              />
              <Input
                label="Jangka Waktu (Expired Date)"
                value={data.expired_date || ""}
                type="date"
                disabled={processing}
                onChange={(e) => setData("expired_date", e.target.value)}
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
            <span className="text-lg font-bold">
              {data.titik_posisi} - {data.expired_date}
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

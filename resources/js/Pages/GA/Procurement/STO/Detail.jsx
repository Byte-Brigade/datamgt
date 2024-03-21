import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import { useFormContext } from "@/Components/Context/FormProvider";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Reports/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
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
  Typography
} from "@material-tailwind/react";
import { useState } from "react";

export default function Page({ auth, sessions, gap_sto_id, periode, semester }) {
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

  const {
    handleFormSubmit,
    setInitialData,
    setUrl,
    setId,
    modalOpen,
    setModalOpen,
    form,
  } = useFormContext();
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isModalEditOpen, setIsModalEditOpen] = useState(false);
  const [isModalDeleteOpen, setIsModalDeleteOpen] = useState(false);
  const [isRefreshed, setIsRefreshed] = useState(false);


  const columns = [
    {
      name: "Cabang",
      field: "branch_name",
      className: "cursor-pointer hover:text-blue-500",
      type: "custom",
      render: (data) => (
        <Link
          href={route("gap.stos.detail.assets", {
            gap_hasil_sto_id: data.gap_hasil_sto_id,
            branch: data.slug,
          })}
        >
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
      name: "Submit",
      field: "detail",
      className: "text-center",
      render: (data) =>
        data.disclaimer ? (
          <a
            className="text-blue-500 hover:underline text-ellipsis"
            href={`/storage/gap/stos/${data.slug}/${periode}/${semester}/${data.disclaimer}`}
            target="__blank"
          >
            {" "}
            {data.disclaimer}
          </a>
        ) : (
          <Button
            onClick={(e) => toggleModalCreate(data.slug)}
            variant="outlined"
          >
            Submit
          </Button>
        ),
    },
  ];




  const handleSubmitExport = (e) => {
    const { branch } = data;
    e.preventDefault();
    window.open(
      route("gap.stos.hasil-sto.export", gap_sto_id),
      "_self"
    );
    setIsModalExportOpen(!isModalExportOpen);
  };


  const handleSubmitEdit = (e) => {
    e.preventDefault();
    put(route("gap.toners.update", data.id), {
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
    destroy(route("gap.toners.delete", data.id), {
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setIsModalDeleteOpen(!isModalDeleteOpen);
      },
    });
  };

  const toggleModalCreate = (id) => {
    setInitialData({ disclaimer: null });
    setUrl("gap.stos.store.hasil_sto");
    setId(id);

    setModalOpen((prevModalOpen) => {
      const updatedModalOpen = {
        ...prevModalOpen,
        ["create"]: !modalOpen.create,
      };
      return updatedModalOpen;
    });
  };


  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };
  const toggleModalEdit = () => {
    setIsModalEditOpen(!isModalEditOpen);
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
          <div className="flex items-center justify-between mb-4">

            {auth.permissions.includes("can export") && (
              <PrimaryButton onClick={toggleModalExport}>
                Create Report
              </PrimaryButton>
            )}
          </div>
          <DataTable
            fetchUrl={`/api/gap/hasil_stos/${gap_sto_id}`}
            columns={columns}
            isRefreshed={isRefreshed}
            bordered={true}

          />

        </div>
      </div>

      {/* Modal Create */}
      <Dialog
        open={modalOpen.create}
        handler={toggleModalCreate}
        size="md"
      >
        <DialogHeader className="flex items-center justify-between">
          Disclaimer
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
        <form onSubmit={handleFormSubmit}>
          <DialogBody divider>
            <div className="flex flex-col gap-y-4">
              <Typography>
                BSM dan BSO menyatakan sudah melakukan STO dengan ini
                bertanggung jawab...
              </Typography>

              <Input
                variant="standard"
                label="Upload Lampiran (.pdf)"
                type="file"
                name="upload"
                id="upload"
                accept=".pdf"
                onChange={(e) =>
                  form.setData("file", e.target.files[0])
                }
              />
            </div>
          </DialogBody>
          <DialogFooter>
            <div className="flex flex-row-reverse gap-x-4">
              <Button disabled={form.processing} type="submit">
                Ubah
              </Button>
              <SecondaryButton
                type="button"
                onClick={toggleModalCreate}
              >
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
          Export Hasil STO Periode
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

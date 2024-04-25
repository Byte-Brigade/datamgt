import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Reports/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentPlusIcon } from "@heroicons/react/24/outline";
import { PlusIcon, XMarkIcon } from "@heroicons/react/24/solid";
import { Head, Link, useForm } from "@inertiajs/react";
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
  const [isRefreshed, setIsRefreshed] = useState(false);

  const columns = [
    {
      name: "Scoring Vendor",
      field: "scoring_vendor",
    },
    {
      name: "Jumlah Vendor",
      field: "jumlah_vendor",
    },
    {
      name: "Q1",
      field: "q1",
    },
    {
      name: "Q2",
      field: "q2",
    },
    {
      name: "Q3",
      field: "q3",
    },
    {
      name: "Q4",
      field: "q4",
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
      name: "Action",
      field: "detail",
      className: "text-center",
      render: (data) => (
        <Link
          href={route("infra.scoring-projects.detail", data.scoring_vendor)}
        >
          <Button variant="outlined">Detail</Button>
        </Link>
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

  const calculateDifference = (startDate, endDate) => {
    let days = 0;
    const tgl_bast = new Date(startDate);
    const tgl_scoring = new Date(endDate);
    if (isNaN(tgl_bast) || isNaN(tgl_bast)) {
      ("tgl tidak valid");
      return;
    }
    const difference = Math.abs(tgl_scoring - tgl_bast); // Menggunakan nilai mutlak
    days = difference / (1000 * 60 * 60 * 24); // Convert milliseconds to days
    (days);
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
    (e.target.value);
  };
  const handleTglScoring = (e) => {
    setData("tgl_scoring", e.target.value);
    calculateDifference(data.tgl_bast, e.target.value);
    (e.target.value);
  };

  const toggleModalImport = () => {
    setIsModalImportOpen(!isModalImportOpen);
  };

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | Assets" />
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
                  <div>
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
                    Create Report
                  </PrimaryButton>
                )}
              </div>
            )}
          <DataTable
            columns={columns}
            fetchUrl={"/api/infra/scoring-projects"}
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
                className="file:border-0 file:text-sm file:font-medium file:text-white file:bg-slate-900 file:hover:opacity-90 file:cursor-pointer cursor-pointer file:rounded-lg file:py-2 file:px-3 !pt-2.5"
                containerProps={{
                  className: "h-fit",
                }}
              />
            </div>
          </DialogBody>
          <DialogFooter className="w-100 flex justify-between">
            <SecondaryButton type="button">
              <a href={route("infra.scoring-projects.template")}>
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
    </AuthenticatedLayout>
  );
}

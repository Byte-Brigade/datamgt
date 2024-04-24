import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentArrowDownIcon } from "@heroicons/react/24/outline";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head, useForm } from "@inertiajs/react";
import {
  Accordion,
  AccordionBody,
  AccordionHeader,
  Button,
  Chip,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
  Typography,
} from "@material-tailwind/react";
import { useState } from "react";

export default function Audit({ auth, sessions }) {
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);
  const [isModalDetailOpen, setIsModalDetailOpen] = useState(false);
  // const [openAcc1, setOpenAcc1] = useState(true);
  // const handleOpenAcc1 = () => setOpenAcc1((cur) => !cur);

  const [openAcc, setOpenAcc] = useState({
    acc1: true,
    acc2: true,
  });

  const handleOpenAcc = (property) =>
    setOpenAcc((prevState) => ({
      ...prevState,
      [property]: !prevState[property],
    }));

  const initialData = {
    id: null,
    log_name: null,
    properties: null,
    causer: null,
    created_at: null,
  };
  const { data, setData } = useForm(initialData);

  const columns = [
    {
      name: "Data",
      field: "log_name",
      type: "custom",
      render: (data) =>
        typeof data.properties === "object" &&
        !Array.isArray(data.properties) ? (
          <button
            className="hover:text-blue-500"
            onClick={() => {
              toggleModalDetail();
              setData(data);
            }}
          >
            {data.log_name}
          </button>
        ) : (
          data.log_name
        ),
      key: (data) => data.id,
    },
    {
      name: "Event",
      field: "event",
      className: "text-center",
      type: "custom",
      render: (data) => (
        <Chip
          value={data.event}
          variant="ghost"
          color={
            data.event === "imported"
              ? "blue"
              : data.event === "created" || data.event === "synced"
              ? "green"
              : data.event === "deleted"
              ? "red"
              : "yellow"
          }
          size="sm"
          className="rounded-full"
        />
      ),
      key: (data) => data.event,
    },
    { name: "Description", field: "description", className: "w-[250px]" },
    { name: "Causer", field: "causer", className: "text-center" },
    {
      name: "Date Time",
      field: "created_at",
      type: "custom",
      className: "text-center",
      render: (data) => convertDate(data.created_at),
      key: (data) => data.created_at,
    },
  ];

  const toggleModalDetail = () => {
    setIsModalDetailOpen(!isModalDetailOpen);
  };

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  const handleSubmitExport = (e) => {
    e.preventDefault();
    setIsModalExportOpen(!isModalExportOpen);
    // window.open(route("ops.branches.export"), "_self");
    alert("cannot export");
  };

  const convertDate = (date) => {
    if (date === null) return "-";
    const d = new Date(date);
    const options = {
      day: "numeric",
      month: "short",
      year: "numeric",
      hour: "numeric",
      minute: "numeric",
    };
    return d.toLocaleDateString("id-ID", options);
  };

  return (
    <AuthenticatedLayout auth={auth}>
      <BreadcrumbsDefault />
      <Head title="Audit Log" />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          {hasRoles("superadmin|admin", auth) &&
            ["can export"].some((permission) =>
              auth.permissions.includes(permission)
            ) && (
              <div className="flex items-center justify-between mb-4">
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
          <DataTable columns={columns} fetchUrl={"/api/audit-log"} />
        </div>
      </div>
      {/* Modal Detail */}
      <Dialog open={isModalDetailOpen} handler={toggleModalDetail} size="md">
        <DialogHeader className="flex items-center justify-between">
          Log Detail
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={toggleModalDetail}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <DialogBody divider>
          <div className="flex flex-col gap-y-4">
            {data.properties?.old ? (
              <Accordion open={openAcc.acc1}>
                <AccordionHeader onClick={() => handleOpenAcc("acc1")}>
                  Old Data
                </AccordionHeader>
                <AccordionBody>
                  {JSON.stringify(data.properties?.old)}
                </AccordionBody>
              </Accordion>
            ) : null}
            {data.properties?.attributes ? (
              <Accordion open={openAcc.acc2}>
                <AccordionHeader onClick={() => handleOpenAcc("acc2")}>
                  New Data
                </AccordionHeader>
                <AccordionBody>
                  {JSON.stringify(data.properties?.attributes)}
                </AccordionBody>
              </Accordion>
            ) : null}
          </div>
        </DialogBody>
        <DialogFooter>
          <div className="flex flex-row-reverse gap-x-4">
            <SecondaryButton type="button" onClick={toggleModalDetail}>
              Tutup
            </SecondaryButton>
          </div>
        </DialogFooter>
      </Dialog>
      {/* Modal Export */}
      <Dialog open={isModalExportOpen} handler={toggleModalExport} size="md">
        <DialogHeader className="flex items-center justify-between">
          Create Report
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
        <DialogBody divider>
          <div className="flex flex-col gap-y-4">
            <Typography>Buat Report Data Cabang?</Typography>
          </div>
        </DialogBody>
        <DialogFooter>
          <div className="flex flex-row-reverse gap-x-4">
            <Button
              onClick={handleSubmitExport}
              // disabled={processing}
              type="submit"
            >
              Buat
            </Button>
            <SecondaryButton type="button" onClick={toggleModalExport}>
              Tutup
            </SecondaryButton>
          </div>
        </DialogFooter>
      </Dialog>
    </AuthenticatedLayout>
  );
}

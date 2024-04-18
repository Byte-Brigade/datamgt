import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentArrowDownIcon } from "@heroicons/react/24/outline";
import { XMarkIcon } from "@heroicons/react/24/solid";
import { Head } from "@inertiajs/react";
import {
  Button,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
  Typography,
} from "@material-tailwind/react";
import Table from "@mui/material/Table";
import TableBody from "@mui/material/TableBody";
import TableCell from "@mui/material/TableCell";
import TableContainer from "@mui/material/TableContainer";
import TableHead from "@mui/material/TableHead";
import TableRow from "@mui/material/TableRow";
import Paper from "@mui/material/Paper";
import { useState } from "react";

export default function Audit({ auth, sessions, activities, userActivities }) {
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);

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

  console.log(activities);

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
          {/* <DataTable
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("superadmin|admin", auth) &&
                ["can edit", "can delete"].some((permission) =>
                  auth.permissions.includes(permission)
                )
                : true
            )}
            fetchUrl={"/api/ops/branches"}
            refreshUrl={isRefreshed}
            className="w-[1500px]"
            component={[
              {
                data: Array.from(
                  new Set(branches.map((branch) => branch.layanan_atm))
                ),
                field: "layanan_atm",
              },
              {
                data: Array.from(
                  new Set(
                    branch_types
                      .filter(
                        (type) => !["SFI", "KF", "KP"].includes(type.type_name)
                      )
                      .map((type) => type.type_name)
                  )
                ),
                field: "type_name",
              },
              {
                data: Array.from(
                  new Set(
                    branches
                      .filter((branch) => branch.area !== null)
                      .map((branch) => branch.area)
                  )
                ),
                field: "area",
              },
            ]}
          /> */}
          <div>
            <TableContainer component={Paper}>
              <Table sx={{ minWidth: 650 }} aria-label="simple table">
                <TableHead>
                  <TableRow>
                    <TableCell>Data</TableCell>
                    <TableCell>Event</TableCell>
                    <TableCell>Description</TableCell>
                    <TableCell>Causer</TableCell>
                    <TableCell>Created At</TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {activities.map((activity) => (
                    <TableRow
                      key={activity.id}
                      sx={{ "&:last-child td, &:last-child th": { border: 0 } }}
                    >
                      <TableCell component="th" scope="row">
                        {activity.log_name}
                      </TableCell>
                      <TableCell>{activity.event}</TableCell>
                      <TableCell>
                        {activity.description}
                      </TableCell>
                      <TableCell>{activity.user ? activity.user : activity.causer_id}</TableCell>
                      <TableCell>{convertDate(activity.created_at)}</TableCell>
                    </TableRow>
                  ))}
                </TableBody>
              </Table>
            </TableContainer>
          </div>
        </div>
      </div>
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

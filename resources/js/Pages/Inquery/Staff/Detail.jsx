import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import PrimaryButton from "@/Components/PrimaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { hasRoles } from "@/Utils/HasRoles";
import { DocumentArrowDownIcon } from "@heroicons/react/24/outline";
import { Head } from "@inertiajs/react";

export default function Detail({ auth, branch, sessions, positions, slug }) {
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
      name: "Join Date",
      field: "hiring_date",
      type: "date",
      className: "text-center w-[300px]",
    },
  ];

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

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  const searchParams = route().params?.search;
  console.log(searchParams);

  return (
    <AuthenticatedLayout auth={auth}>
      <BreadcrumbsDefault />
      <Head title="Karyawan Bank OPS Cabang" />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
            <h2 className="text-xl font-semibold text-center">
              {branch.branch_name}
            </h2>
          <DataTable
            columns={columns.filter((column) =>
              column.field === "action"
                ? hasRoles("superadmin|admin|branch_ops", auth) &&
                  ["can edit", "can delete"].some((permission) =>
                    auth.permissions.includes(permission)
                  )
                : true
            )}
            parameters={
              searchParams && {
                search: searchParams,
              }
            }
            fetchUrl={`/api/inquery/staff/${slug}`}
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
      {/* Modal Export */}
      {/* <Dialog open={isModalExportOpen} handler={toggleModalExport} size="md">
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
            <div className="flex flex-col gap-y-4">Export</div>
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
      </Dialog> */}
    </AuthenticatedLayout>
  );
}

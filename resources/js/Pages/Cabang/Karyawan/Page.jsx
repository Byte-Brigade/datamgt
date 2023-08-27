import { Pagination, Search, TableHeader } from "@/Components/DataTable";
import Modal from "@/Components/Modal";
import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import SelectInput from "@/Components/SelectInput";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, router, useForm, usePage } from "@inertiajs/react";
import { pickBy } from "lodash";
import { useRef, useState } from "react";

export default function Karyawan({ employees, branches, positions, sessions }) {
  const { data, setData, post, processing, errors } = useForm({
    file: null,
    branch: 0,
    position: 0,
  });
  const perpage = useRef(employees.per_page);
  const { url } = usePage();
  const [loading, setLoading] = useState(false);
  const [search, setSearch] = useState("");
  const [isModalImportOpen, setIsModalImportOpen] = useState(false);
  const [isModalExportOpen, setIsModalExportOpen] = useState(false);

  const headers = [
    { name: "No", field: "id" },
    { name: "Branch ID", field: "branch_code" },
    { name: "Branch Name", field: "branch_name" },
    { name: "Position", field: "position" },
    { name: "Employee ID", field: "employee_id" },
    { name: "Employee Name", field: "employee_name" },
    { name: "Email", field: "email" },
    { name: "Gender", field: "gender" },
    { name: "Tanggal Lahir", field: "tanggal_lahir" },
    { name: "Hiring Date", field: "hiring_date" },
    { name: "Action", field: "action" },
  ];

  const submit = (e) => {
    e.preventDefault();
    post(route("employees.import"));
  };

  const exportData = (e) => {
    e.preventDefault();
    const { branch, position } = data;
    const query =
      branch !== 0 && position !== 0
        ? `?branch=${branch}&position=${position}`
        : branch !== 0
        ? `?branch=${branch}`
        : position !== 0
        ? `?position=${position}`
        : "";

    window.open(route("employees.export") + query, "_self");
    setData({ branch: 0, position: 0 });
  };

  const handleChangePerpage = (e) => {
    perpage.current = e.target.value;
    getData();
  };

  const handleSearch = (e) => {
    e.preventDefault();
    getData();
  };

  const getData = () => {
    setLoading(true);
    router.get(
      route().current(),
      pickBy({
        perpage: perpage.current,
        search,
      }),
      {
        preserveScroll: true,
        preserveState: true,
        onFinish: () => setLoading(false),
      }
    );
  };

  const convertDate = (date) => {
    const d = new Date(date);
    const options = {
      day: "numeric",
      month: "short",
      year: "numeric",
    };
    return d.toLocaleDateString("id-ID", options);
  };

  const toggleModalImport = () => {
    setIsModalImportOpen(!isModalImportOpen);
  };

  const toggleModalExport = () => {
    setIsModalExportOpen(!isModalExportOpen);
  };

  console.log(employees);
  return (
    <AuthenticatedLayout>
      <Head title="Karyawan Bank OPS Cabang" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>
            {sessions.status && (
              <p className="font-semibold text-green-400">{sessions.message}</p>
            )}
          </div>
          <div className="flex items-center justify-between mb-4">
            <PrimaryButton
              className="bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"
              onClick={toggleModalImport}
            >
              <div className="flex items-center gap-x-1">
                <svg
                  xmlns="http://www.w3.org/2000/svg"
                  className="icon icon-tabler icon-tabler-plus"
                  width="20"
                  height="20"
                  viewBox="0 0 24 24"
                  strokeWidth="2"
                  stroke="currentColor"
                  fill="none"
                  strokeLinecap="round"
                  strokeLinejoin="round"
                >
                  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                  <path d="M12 5l0 14"></path>
                  <path d="M5 12l14 0"></path>
                </svg>
                Import Excel
              </div>
            </PrimaryButton>
            <PrimaryButton onClick={toggleModalExport}>
              Create Report
            </PrimaryButton>
          </div>
          <Search
            perpage={perpage}
            search={search}
            setSearch={setSearch}
            handleSearch={handleSearch}
            handleChangePerpage={handleChangePerpage}
          />
          <div className="relative overflow-x-auto border-2 rounded-lg border-slate-200">
            <table className="w-full text-sm">
              <TableHeader headers={headers} />
              <tbody>
                {loading ? (
                  <tr>
                    <td
                      className="p-4 text-lg font-semibold text-center transition-colors duration-75 bg-slate-200 animate-pulse"
                      colSpan="11"
                    >
                      Loading ...
                    </td>
                  </tr>
                ) : (
                  employees.data.map((employee, index) => (
                    <tr
                      key={employee.id}
                      className="[&>td]:p-2 hover:bg-slate-200"
                    >
                      <td>{employees.from + index}</td>
                      <td>{employee.branches.branch_code}</td>
                      <td>{employee.branches.branch_name}</td>
                      <td>{employee.positions.position_name}</td>
                      <td>{employee.employee_id}</td>
                      <td>{employee.name}</td>
                      <td>{employee.email}</td>
                      <td className="text-center">{employee.gender}</td>
                      <td>
                        {employee.birth_date
                          ? convertDate(employee.birth_date)
                          : "-"}
                      </td>
                      <td>
                        {employee.hiring_date
                          ? convertDate(employee.hiring_date)
                          : "-"}
                      </td>
                      <td>Edit | Delete</td>
                    </tr>
                  ))
                )}
              </tbody>
            </table>
          </div>
          <Pagination data={employees} url={url} />
        </div>
      </div>
      <Modal show={isModalImportOpen}>
        <div className="flex flex-col p-4 gap-y-4">
          <h3 className="text-xl font-semibold text-center">Import Data</h3>
          <form onSubmit={submit} encType="multipart/form-data">
            <div className="flex flex-col">
              <label htmlFor="import">Import Excel (.xlsx)</label>
              <input
                className="bg-gray-100 border-2 border-gray-200 rounded-lg"
                onChange={(e) => setData("file", e.target.files[0])}
                type="file"
                name="import"
                id="import"
                accept=".xlsx"
              />
            </div>
            <div className="flex justify-between mt-4 gap-x-4">
              <SecondaryButton type="button" onClick={toggleModalImport}>
                Close Modal
              </SecondaryButton>
              <PrimaryButton
                type="submit"
                onClick={toggleModalImport}
                disabled={processing}
              >
                Import Data
              </PrimaryButton>
            </div>
          </form>
        </div>
      </Modal>
      <Modal show={isModalExportOpen}>
        <div className="flex flex-col p-4 gap-y-4">
          <h3 className="text-xl font-semibold text-center">Create Report</h3>
          <form onSubmit={exportData}>
            <div className="flex flex-col px-12">
              <p>Export to Excel (.xlsx)</p>
              <div className="flex flex-col gap-y-2">
                <div className="grid items-center grid-cols-2">
                  <label htmlFor="branch">Branch :</label>
                  <SelectInput
                    id="branch"
                    name="branch"
                    onChange={(e) => setData("branch", e.target.value)}
                    value={data.branch}
                  >
                    <option value={0}>All</option>
                    {branches.map((branch) => (
                      <option key={branch.id} value={branch.id}>
                        {branch.branch_code} - {branch.branch_name}
                      </option>
                    ))}
                  </SelectInput>
                </div>
                <div className="grid items-center grid-cols-2">
                  <label htmlFor="position">Position :</label>
                  <SelectInput
                    id="position"
                    name="position"
                    onChange={(e) => setData("position", e.target.value)}
                    value={data.position}
                  >
                    <option value={0}>All</option>
                    {positions.map((position) => (
                      <option key={position.id} value={position.id}>
                        {position.position_name}
                      </option>
                    ))}
                  </SelectInput>
                </div>
              </div>
            </div>
            <div className="flex justify-between mt-4 gap-x-4">
              <SecondaryButton type="button" onClick={toggleModalExport}>
                Close Modal
              </SecondaryButton>
              <PrimaryButton
                type="submit"
                onClick={toggleModalExport}
                disabled={processing}
              >
                Create Report
              </PrimaryButton>
            </div>
          </form>
        </div>
      </Modal>
    </AuthenticatedLayout>
  );
}

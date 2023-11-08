import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { BuildingOffice2Icon } from "@heroicons/react/24/outline";
import { UserGroupIcon } from "@heroicons/react/24/solid";
import { Head } from "@inertiajs/react";
import { Option, Select, Typography } from "@material-tailwind/react";
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend,
} from "chart.js";
import { useState } from "react";
import { Bar } from "react-chartjs-2";
import ChartDataLabels from "chartjs-plugin-datalabels";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

export default function Dashboard({
  auth,
  errors,
  sessions,
  data,
  branches,
  dataCabang,
}) {
  const [branchId, setBranchId] = useState(0);
  const [area, setArea] = useState("none");
  const [active, setActive] = useState("cabang");
  const [totalBranch, setTotalBranch] = useState(0);
  const options = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      datalabels: {
        anchor: "end",
        align: "end",
        formatter: (value, context) => {
          return value; // Menampilkan nilai data di dalam bar chart
        },
      },
      legend: {
        position: "top",
      },
    },
    scales: {
      y: {
        display: false,
        grid: {
          display: false,
        },
      },
      x: {
        grid: {
          display: false,
        },
      },
    },
  };

  const columns = [
    { name: "Kantor Pusat (KP)", field: "kantor_pusat" },
    { name: "Kantor Cabang (KC)", field: "kantor_cabang" },
    { name: "Kantor Cabang Pembantu", field: "kantor_cabang_pembantu" },
    {
      name: "Kantor Fungsional Operasional (KFO)",
      field: "kantor_fungsional_operasional",
    },
    {
      name: "Kantor Fungsional Non Operasional (KFNO)",
      field: "kantor_fungsional_non_operasional",
    },
  ];

  const handleFilterBranch = (id) => {
    setBranchId(parseInt(id));
  };
  const handleFilterArea = (value) => {
    console.log(value);
    setArea(value);
  };

  let labels = data.employee_positions.map(
    (position) => position.position_name
  );

  const test = {
    labels,
    datasets: [
      {
        label: "Jumlah Karyawan",
        data: labels.map((label) =>
          branchId
            ? data.employees.filter(
                (employee) =>
                  employee.employee_positions.position_name === label &&
                  employee.branch_id === branchId &&
                  (area === "none" || employee.branches.area === area)
              ).length
            : data.employees.filter(
                (employee) =>
                  employee.employee_positions.position_name === label &&
                  (area === "none" || employee.branches.area === area)
              ).length
        ),
        backgroundColor: "rgba(255, 56, 56  , 1)",
      },
    ],
  };
  labels = Object.keys(data.jumlah_cabang_alt);
  const branchChart = {
    labels,
    datasets: [
      {
        label: "Jumlah Cabang",
        data: labels.map(
          (label) =>
            data.jumlah_cabang_alt[label].filter(
              (branch) =>
                (branchId === 0 || branch.id === branchId) &&
                (area === "none" || branch.area === area)
            ).length
        ),
        backgroundColor: "rgba(255, 56, 56  , 1)",
      },
    ],
  };
  labels = Object.keys(data.jumlah_atm);
  const atmChart = {
    labels,
    datasets: [
      {
        label: "Jumlah ATM",
        data: labels.map(
          (label) =>
            data.jumlah_atm[label].filter(
              (branch) =>
                (branchId === 0 || branch.id === branchId) &&
                (area === "none" || branch.area === area)
            ).length
        ),
        backgroundColor: "rgba(255, 56, 56  , 1)",
      },
    ],
  };

  console.log(data);
  return (
    <AuthenticatedLayout auth={auth} errors={errors}>
      <Head title="Dashboard" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex flex-col">
            <h2 className="text-2xl font-semibold text-left w-80">Dashboard</h2>
            <div className="flex my-4 gap-x-4">
              <Select
                label="Area"
                value=""
                onChange={(e) => handleFilterArea(e)}
                className="bg-white"
              >
                {data.areas.map((area, index) => {
                  if (index === 0) {
                    return (
                      <Option key={0} value="none">
                        All
                      </Option>
                    );
                  }
                  return (
                    <Option key={index} value={`${area}`}>
                      {area}
                    </Option>
                  );
                })}
              </Select>
              <Select
                label="Branch"
                value=""
                onChange={(e) => handleFilterBranch(e)}
                className="bg-white"
              >
                <Option key={0} value="0">
                  All
                </Option>

                {data.branches
                  .filter((branch) => area === "none" || branch.area === area)
                  .map((branch, index) => {
                    return (
                      <Option key={index} value={`${branch.id}`}>
                        {branch.branch_code} - {branch.branch_name}
                      </Option>
                    );
                  })}
              </Select>
            </div>
          </div>
          <div className="grid grid-cols-4 gap-x-4">
            <div
              onClick={() => setActive("cabang")}
              className="cursor-pointer flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl"
            >
              <BuildingOffice2Icon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">Jumlah Cabang</Typography>
                <Typography>
                  {branchId
                    ? data.branches.filter(
                        (branch) =>
                          (branch.id == branchId && area === "none") ||
                          branch.area === area
                      ).length
                    : data.branches.filter(
                        (branch) => area === "none" || branch.area === area
                      ).length}
                </Typography>
              </div>
            </div>
            <div
              onClick={() => setActive("atm")}
              className="cursor-pointer flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl"
            >
              <BuildingOffice2Icon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">Jumlah ATM</Typography>
                <Typography>
                  {Object.keys(data.jumlah_atm).reduce((acc, atm) => {
                    return (
                      acc +
                      data.jumlah_atm[atm].filter(
                        (branch) =>
                          (branchId === 0 || branch.id === branchId) &&
                          (area === "none" || branch.area === area)
                      ).length
                    );
                  }, 0)}
                </Typography>
              </div>
            </div>
            <div
              onClick={() => setActive("karyawan")}
              className="cursor-pointer flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl"
            >
              <UserGroupIcon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">Jumlah Karyawan</Typography>
                <Typography>
                  {branchId
                    ? data.jumlahKaryawan.filter(
                        (employee) =>
                          employee.branch_id == branchId &&
                          (area === "none" || employee.branches.area === area)
                      ).length
                    : data.jumlahKaryawan.filter(
                        (employee) =>
                          area === "none" || employee.branches.area === area
                      ).length}
                </Typography>
              </div>
            </div>
            <div
              onClick={() => setActive("asset")}
              className="cursor-pointer flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl"
            >
              <UserGroupIcon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">Jumlah Asset</Typography>
                <Typography>
                  {branchId
                    ? data.jumlahKaryawanBSO.filter(
                        (karyawan) => karyawan.branch_id == branchId
                      ).length
                    : data.jumlahKaryawanBSO.length}
                </Typography>
              </div>
            </div>
          </div>
          {active === "cabang" && (
            <div className="pt-4 w-full h-[200px] grid grid-cols-2 gap-4">
              <div className="cols-span-1">
                <Bar
                  options={options}
                  data={branchChart}
                  plugins={[ChartDataLabels]}
                />
              </div>
              <div className="cols-span-1">
                {/* Tabel Cabang */}
                <table className={`text-sm leading-3 bg-white w-full`}>
                  <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="text-center">Tipe Cabang</th>
                      <th className="text-center">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody className="overflow-y-auto">
                    <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                      <td>Kantor Pusat</td>
                      <td>1</td>
                    </tr>
                    {Object.keys(data.jumlah_cabang).map((cabang) => {
                      return (
                        <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                          <td>{cabang}</td>
                          <td>{data.jumlah_cabang[cabang].length}</td>
                        </tr>
                      );
                    })}

                    <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                      <td>
                        <strong>Total</strong>
                      </td>
                      <td>
                        <strong>
                          {Object.keys(data.jumlah_cabang).reduce(
                            (acc, item) => {
                              return acc + data.jumlah_cabang[item].length;
                            },
                            1
                          )}
                        </strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Tabel Karyawan */}
          {active === "karyawan" && (
            <div className="pt-4 w-full h-[200px] grid grid-cols-2 gap-4">
              <div className="cols-span-1">
                <Bar
                  options={options}
                  data={test}
                  plugins={[ChartDataLabels]}
                />
              </div>

              <div className="cols-span-1">
                <table className={`text-sm leading-3 bg-white w-full`}>
                  <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="text-center">Jabatan</th>
                      <th className="text-center">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody className="overflow-y-auto">
                    {data.employee_positions.map((position) => (
                      <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                        <td className="text-center">
                          {position.position_name}
                        </td>
                        <td className="text-center">
                          {
                            data.employees
                              .filter(
                                (employee) =>
                                  employee.employee_positions.position_name ===
                                  position.position_name
                              )
                              .filter(
                                (employee) =>
                                  area === "none" ||
                                  employee.branches.area === area
                              ).length
                          }
                        </td>
                      </tr>
                    ))}
                    <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                      <td className="text-center">
                        <strong>Total</strong>
                      </td>
                      <td className="text-center">
                        <strong>
                          {
                            data.employees.filter(
                              (employee) =>
                                area === "none" ||
                                employee.branches.area === area
                            ).length
                          }
                        </strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Tabel ATM */}
          {active === "atm" && (
            <div className="pt-4 w-full h-[200px] grid grid-cols-2 gap-4">
              <div className="cols-span-1">
                <Bar
                  options={options}
                  data={atmChart}
                  plugins={[ChartDataLabels]}
                />
              </div>
              <div className="cols-span-1">
                <table className={`text-sm leading-3 bg-white w-full`}>
                  <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="text-center">Fungsi</th>
                      <th className="text-center">Jumlah</th>
                    </tr>
                  </thead>
                  <tbody className="overflow-y-auto">
                    {Object.keys(data.jumlah_atm).map((atm) => (
                      <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                        <td className="text-center">{atm}</td>
                        <td className="text-center">
                          {
                            data.jumlah_atm[atm].filter(
                              (branch) =>
                                (branchId === 0 || branch.id === branchId) &&
                                (area === "none" || branch.area === area)
                            ).length
                          }
                        </td>
                      </tr>
                    ))}

                    <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                      <td className="text-center">
                        <strong>Total</strong>
                      </td>
                      <td className="text-center">
                        <strong>
                          {Object.keys(data.jumlah_atm).reduce((acc, atm) => {
                            return (
                              acc +
                              data.jumlah_atm[atm].filter(
                                (branch) =>
                                  (branchId === 0 || branch.id === branchId) &&
                                  (area === "none" || branch.area === area)
                              ).length
                            );
                          }, 0)}
                        </strong>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          )}

          {/* Jumlah Asset */}
          {active === "asset" && (
            <table className={`text-sm leading-3 bg-white`}>
              <thead className="sticky top-0 border-b-2 table-fixed border-slate-200">
                <tr className="[&>th]:p-2 bg-slate-100">
                  <th className="text-center" rowSpan={2} colSpan={2}>
                    Lokasi
                  </th>
                  <th className="text-center" colSpan={4}>
                    Kategori A (Depresiasi)
                  </th>
                  <th className="text-center" colSpan={2}>
                    Kategori B (Non-Depresiasi)
                  </th>
                  {/* Lokasi: Kantor Pusat, Cabang */}
                  {/* Kategori A (Asset Depresiasi) */}
                  {/* Kategori A (Asset Non-Depresiasi) */}
                </tr>
                <tr className="[&>th]:p-2 bg-slate-100">
                  <th className="text-center">Item</th>
                  <th className="text-center">Nilai Perolehan</th>
                  <th className="text-center">Penyusutan</th>
                  <th className="text-center">Net Book Value</th>
                  <th className="text-center">Item</th>
                  <th className="text-center">Nilai Perolehan</th>
                </tr>
              </thead>
              <tbody className="overflow-y-auto">
                {Object.keys(data.assets).map((kategori, index) => (
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="text-center" key={index} colSpan={2}>
                      {kategori}
                    </td>
                    {data.assets[kategori].map((item, index) => (
                      <>
                        <td className="text-center">{item.item}</td>
                        <td className="text-center">{item.nilai_perolehan}</td>
                        {item.penyusutan && (
                          <td className="text-center">{item.penyusutan}</td>
                        )}
                        {item.network_value && (
                          <td className="text-center">{item.network_value}</td>
                        )}
                      </>
                    ))}
                  </tr>
                ))}
              </tbody>
            </table>
          )}
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

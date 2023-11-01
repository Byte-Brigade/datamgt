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

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

export default function Dashboard({ auth, errors, sessions, data, branches }) {
  const [branchId, setBranchId] = useState(0);
  const options = {
    responsive: true,
    plugins: {
      legend: {
        position: "top",
      },
      title: {
        display: true,
        text: "Jumlah Karyawan BSS",
      },
    },
    scales: {
      y: {
        beginAtZero: true,
        ticks: {
          stepSize: 1,
          precision: 0,
        },
        grid: {
          display:false,
        }
      },
      x: {
        grid: {
          display:false,
        }
      }
    }
  };

  const handleFilterBranch = (id) => {
    setBranchId(parseInt(id));
  };

  const labels = data.employee_positions.map(
    (position) => position.position_name
  );

  const test = {
    labels,
    datasets: [
      {
        label: "Karyawan",
        data: labels.map(
          (label) => branchId ?
            data.employees.filter(
              (employee) => employee.employee_positions.position_name === label
            ).filter(employee => employee.branch_id === branchId).length : data.employees.filter(
              (employee) => employee.employee_positions.position_name === label
            ).length
        ),
        backgroundColor: "rgba(150, 255, 230  , 1)",
      },
    ],
  };

  console.log(data);
  return (
    <AuthenticatedLayout auth={auth} errors={errors}>
      <Head title="Dashboard" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>
          <div className="flex items-center justify-between mb-4 ">
            <h2 className="text-2xl font-semibold text-left w-80">Dashboard</h2>
            <Select
              label="Branch"
              value={`${data.branch_id}`}
              onChange={(e) => handleFilterBranch(e)}
            >

              {data.branches.map((branch, index) => {
                if (index + 1 === 1) {
                  return (<Option key={0} value="0">
                    All
                  </Option>)
                }
                return (<Option key={branch.id} value={`${branch.id}`}>
                  {branch.branch_code} - {branch.branch_name}
                </Option>)
              })}
            </Select>
          </div>
          <div className="grid grid-cols-4 gap-x-4">
            <div className="flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl">
              <BuildingOffice2Icon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">Jumlah Cabang</Typography>
                <Typography>
                  {branchId
                    ? data.branches.filter((branch) => branch.id == branchId)
                      .length
                    : data.branches.length}
                </Typography>
              </div>
            </div>
            <div className="flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl">
              <BuildingOffice2Icon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">
                  Jumlah Layanan ATM (24 Jam)
                </Typography>
                <Typography>
                  {branchId
                    ? data.jumlahATM24Jam.filter(
                      (branch) => branch.id == branchId
                    ).length
                    : data.jumlahATM24Jam.length}
                </Typography>
              </div>
            </div>
            <div className="flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl">
              <UserGroupIcon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">Jumlah Karyawan</Typography>
                <Typography>
                  {branchId
                    ? data.jumlahKaryawan.filter(
                      (karyawan) => karyawan.branch_id == branchId
                    ).length
                    : data.jumlahKaryawan.length}
                </Typography>
              </div>
            </div>
            <div className="flex items-center px-4 py-2 bg-white border gap-x-4 border-slate-400 rounded-xl">
              <UserGroupIcon className="w-10 h-10" />
              <div className="flex flex-col">
                <Typography variant="h5">Jumlah BSO</Typography>
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
          <Bar options={options} data={test} />
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

import {
  BarElement,
  CategoryScale,
  Chart as ChartJS,
  Legend,
  LinearScale,
  Title,
  Tooltip,
} from "chart.js";
import ChartDataLabels from "chartjs-plugin-datalabels";
import { Bar } from "react-chartjs-2";

ChartJS.register(
  CategoryScale,
  LinearScale,
  BarElement,
  Title,
  Tooltip,
  Legend
);

export default function BarChart({
  data,
  label,
  branchState,
  areaState,
  type,
}) {
  const options = {
    responsive: true,
    maintainAspectRatio: true,
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

  const branchLabels = Object.keys(data.jumlah_cabang_alt);
  const employeeLabels = data.employee_positions.map(
    (position) => position.position_name
  );
  const atmLabels = Object.keys(data.jumlah_atm);

  const labels =
    type === "branch"
      ? branchLabels
      : type === "employee"
      ? employeeLabels
      : type === "atm"
      ? atmLabels
      : [];
  const backgroundColor = "rgba(255, 56, 56, 1)";

  const setData = labels.map((label) => {
    switch (type) {
      case "branch":
        return data.jumlah_cabang_alt[label].filter(
          (branch) =>
            (branchState === 0 || branch.id === branchState) &&
            (areaState === "none" || branch.area === areaState)
        ).length;
      case "employee":
        return branchState
          ? data.employees.filter(
              (employee) =>
                employee.employee_positions.position_name === label &&
                employee.branch_id === branchState &&
                (areaState === "none" || employee.branches.area === areaState)
            ).length
          : data.employees.filter(
              (employee) =>
                employee.employee_positions.position_name === label &&
                (areaState === "none" || employee.branches.area === areaState)
            ).length;
      case "atm":
        return data.jumlah_atm[label].filter(
          (branch) =>
            (branchState === 0 || branch.id === branchState) &&
            (areaState === "none" || branch.area === areaState)
        ).length;
      default:
        return [];
    }
  });

  const datasets = [
    {
      label,
      data: setData,
      backgroundColor,
    },
  ];

  const chart = { labels, datasets };

  return <Bar options={options} data={chart} plugins={[ChartDataLabels]} />;
}

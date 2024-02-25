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
  height,
}) {
  const options = {
    indexAxis: type === "employee" ? "y" : "x",
    responsive: true,
    maintainAspectRatio: type === "employee" ? false : true,
    plugins: {
      datalabels: {
        anchor: "end",
        align: "end",
        formatter: (value, context) => {
          return value; // Menampilkan nilai data di dalam bar chart
        },
      },
      legend: {
        position: type === "employee" ? "none" : "top",
      },
    },
    scales: {
      y: {
        display: type === "employee" ? true : false,
        grid: {
          display: false,
        },
        ticks: {
          callback: (value, index, values) => {
            return truncatedEmployeeLabels[index];
          },
        },
      },
      x: {
        grid: {
          display: false,
        },
      },
    },
    layout: {
      padding: {
        left: 10,
        right: 10,
        top: 10,
        bottom: 10,
      },
    },
  };

  const branchLabels = Object.keys(data.jumlah_cabang_alt);
  const employeeLabels = data.employee_positions
    .map((position) => position.position_name)
    .sort();

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
        ).length > 0 ? data.jumlah_cabang_alt[label].filter(
          (branch) =>
            (branchState === 0 || branch.id === branchState) &&
            (areaState === "none" || branch.area === areaState)
        ).length : 0;
      case "employee":
        return branchState
          ? data.employees
              .sort()
              .filter(
                (employee) =>
                  employee.employee_positions.position_name === label &&
                  employee.branch_id === branchState &&
                  (areaState === "none" ||
                    employee.branches.area === areaState) &&
                  !["BM", "BSM", "BSO"].includes(
                    employee.employee_positions.position_name
                  )
              ).length
          : data.employees.filter(
              (employee) =>
                employee.employee_positions.position_name === label &&
                (areaState === "none" ||
                  employee.branches.area === areaState) &&
                !["BM", "BSM", "BSO"].includes(
                  employee.employee_positions.position_name
                )
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

  const datasetsObj = labels.reduce((acc, label, index) => {
    const val = setData[index];

    return val > 0 ? { ...acc, [label]: val } : acc;
  }, {});

  const newLabels = Object.keys(datasetsObj);


  const truncatedEmployeeLabels = newLabels.map((label) =>
    label.length > 25 ? label.substring(0, 25) + "..." : label
  );

  const datasets = [
    {
      label,
      data: type === "employee" ? Object.values(datasetsObj).filter(item => item > 0) : setData.filter(item => item > 0),
      backgroundColor,
    },
  ];



  const chart = { labels: newLabels, datasets };
  return (
    <Bar
      options={options}
      data={chart}
      plugins={[ChartDataLabels]}
      height={height}
    />
  );
}

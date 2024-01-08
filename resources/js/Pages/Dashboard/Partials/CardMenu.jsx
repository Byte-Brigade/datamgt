import { Card, CardBody, CardHeader, Typography } from "@material-tailwind/react";

export default function CardMenu({
  Icon,
  label,
  type,
  active,
  onClick,
  branchState,
  areaState,
  color = "blue" | "green" | "orange" | "purple",
  data,
}) {
  const iconColor =
    color === "blue"
      ? "text-cyan-800"
      : color === "green"
      ? "text-green-800"
      : color === "orange"
      ? "text-orange-800"
      : color === "purple"
      ? "text-deep-purple-800"
      : "";

  const outlineColor =
    color === "blue"
      ? "bg-cyan-100"
      : color === "green"
      ? "bg-green-100"
      : color === "orange"
      ? "bg-orange-100"
      : color === "purple"
      ? "bg-deep-purple-100"
      : "bg-slate-100";

  const ringColor =
    color === "blue"
      ? "ring-cyan-500"
      : color === "green"
      ? "ring-green-500"
      : color === "orange"
      ? "ring-orange-500"
      : color === "purple"
      ? "ring-deep-purple-500"
      : "ring-cyan-500";

  const setData =
    type === "branch"
      ? data.branches.filter(
          (branch) =>
            (branchState === 0 || branch.id === branchState) &&
            (areaState === "none" || branch.area === areaState)
        ).length
      : type === "atm"
      ? Object.keys(data.jumlah_atm).reduce((acc, atm) => {
          return (
            acc +
            data.jumlah_atm[atm].filter(
              (branch) =>
                (branchState === 0 || branch.id === branchState) &&
                (areaState === "none" || branch.area === areaState)
            ).length
          );
        }, 0)
      : type === "employee"
      ? branchState
        ? data.jumlahKaryawan.filter(
            (employee) =>
              employee.branch_id == branchState &&
              (areaState === "none" || employee.branches.area === areaState)
          ).length
        : data.jumlahKaryawan.filter(
            (employee) =>
              areaState === "none" || employee.branches.area === areaState
          ).length
      : type === "asset"
      ? data.assets.filter(
          (asset) =>
            (branchState === 0 || asset.branch_id == branchState) &&
            (areaState === "none" || asset.area == areaState)
        ).length
      : type === "gap_scorings"
      ? data.gap_scorings.filter(
          (asset) =>
            (branchState === 0 || asset.branch_id == branchState) &&
            (areaState === "none" || asset.branches.area == areaState)
        ).length
      : [];

  return (
    <Card
      onClick={onClick}
      className={`flex-row cursor-pointer gap-x-4 ${
        active === type
          ? "hover:bg-slate-200 bg-slate-100 ring-2 ring-offset-2 " + ringColor
          : "bg-white hover:bg-slate-200"
      } transition-all duration-300 w-full max-w-[24-rem]`}
    >
      <CardHeader shadow={false} floated={false} className={`m-0 w-2/5 shrink-0 rounded-r-none ${outlineColor}`}>
        <Icon className={`h-full w-full object-cover ${iconColor}`} />
      </CardHeader>
      <CardBody className={`p-4 ${setData.length != 0 ? '' : 'flex items-center'}`}>
        <Typography variant="h5" color="black" className={setData.length ? 'mb-2' : ''}>{label}</Typography>
        <Typography color="black">{setData}</Typography>
      </CardBody>
    </Card>
  );
}

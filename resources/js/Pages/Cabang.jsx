import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, usePage } from "@inertiajs/react";
import CardMenu from "./Dashboard/Partials/CardMenu";
import {
  ArchiveBoxIcon,
  BuildingOffice2Icon,
  CreditCardIcon,
  UserGroupIcon,
} from "@heroicons/react/24/outline";
import { useState } from "react";
import {
  Card,
  CardBody,
  CardHeader,
  Typography,
} from "@material-tailwind/react";
import { useRef } from "react";
import { useEffect } from "react";

export default function Branch({ sessions, auth }) {
  const { url } = usePage();
  const [active, setActive] = useState("branch");
  const [selected, setSelected] = useState();
  const contentRef = useRef(null);

  const handleItemClick = (item) => {
    setSelected(item);
  };

  const cardMenus = [
    {
      label: "Jumlah Cabang",
      type: "branch",
      Icon: BuildingOffice2Icon,
      active,
      onClick: () => handleItemClick("branch"),
      color: "blue",
    },
    {
      label: "Jumlah ATM",
      type: "atm",
      Icon: CreditCardIcon,
      active,
      onClick: () => handleItemClick("atm"),
      color: "green",
    },
    {
      label: "Jumlah Karyawan",
      type: "employee",
      Icon: UserGroupIcon,
      active,
      onClick: () => handleItemClick("employee"),
      color: "orange",
    },
    {
      label: "Jumlah Asset",
      type: "asset",
      Icon: ArchiveBoxIcon,
      active,
      onClick: () => handleItemClick("asset"),
      color: "purple",
    },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Branch" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div className="grid grid-cols-4 gap-x-2">
            {cardMenus.map((menu, index) => (
              <CardList
                key={index}
                label={menu.label}
                type={menu.type}
                Icon={menu.Icon}
                active={active}
                onClick={menu.onClick}
                color={menu.color}
              />
            ))}
          </div>
          <div ref={contentRef}>{selected === "branch" && <p id="branch">1</p>}</div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

const CardList = ({
  Icon,
  label,
  type,
  active,
  onClick,
  color = "blue" | "green" | "orange" | "purple",
}) => {
  return (
    <Card
      onClick={onClick}
      className={`flex-row cursor-pointer gap-x-4 ${
        active === type
          ? "hover:bg-slate-200 bg-slate-100 ring-2 ring-offset-2 "
          : "bg-white hover:bg-slate-200"
      } transition-all duration-300 w-full max-w-[24-rem]`}
    >
      <CardHeader
        shadow={false}
        floated={false}
        className={`m-0 w-2/5 shrink-0 rounded-r-none`}
      >
        <Icon className={`h-full w-full object-cover`} />
      </CardHeader>
      <CardBody className="p-4">
        <Typography variant="h5" color="black" className="mb-2">
          {label}
        </Typography>
        <Typography color="black">Lorem ipsum</Typography>
      </CardBody>
    </Card>
  );
};

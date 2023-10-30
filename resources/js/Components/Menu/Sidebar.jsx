import { ChevronDownIcon, ChevronRightIcon } from "@heroicons/react/24/outline";
import {
  PresentationChartBarIcon,
  UserGroupIcon,
} from "@heroicons/react/24/solid";
import { Link, usePage } from "@inertiajs/react";
import {
  Accordion,
  AccordionBody,
  AccordionHeader,
  List,
  ListItem,
  ListItemPrefix,
  Tooltip,
  Typography,
} from "@material-tailwind/react";
import { useState } from "react";

export function SidebarWithLogo({ sidebarOpen, setSidebarOpen }) {
  const { auth } = usePage().props;
  const [open, setOpen] = useState(3);
  const [openAcc1, setOpenAcc1] = useState(route().current("inquery.*"));
  const [openAcc2, setOpenAcc2] = useState(route().current("reporting.*"));
  const [openAcc3, setOpenAcc3] = useState(
    route().current("ops.*") ||
      route().current("gap.*") ||
      route().current("infra.*")
  );

  const [openAcc4, setOpenAcc4] = useState(route().current("ops.*"));
  const [openAcc5, setOpenAcc5] = useState(route().current("gap.*"));
  const [openAcc6, setOpenAcc6] = useState(route().current("infra.*"));

  const handleOpenAcc = (acc) => {
    switch (acc) {
      case 1:
        setOpenAcc1((cur) => !cur);
        break;
      case 2:
        setOpenAcc2((cur) => !cur);
        break;
      case 3:
        setOpenAcc3((cur) => !cur);
        break;
      case 4:
        setOpenAcc4((cur) => !cur);
        break;
      case 5:
        setOpenAcc5((cur) => !cur);
        break;
      case 6:
        setOpenAcc6((cur) => !cur);
        break;
      default:
        break;
    }
  };

  const handleOpen = (value) => {
    setOpen(open === value ? 0 : value);
  };

  const inqueryRouter = [
    { name: "Branch", path: "inquery.branch" },
    { name: "Staff", path: "maintenance" },
    { name: "Asset", path: "maintenance" },
    { name: "Lisensi", path: "maintenance" },
  ];

  const reportRouter = [
    { name: "Branch", path: "reporting.branches" },
    { name: "Asset", path: "maintenance" },
    { name: "Lisensi", path: "maintenance" },
  ];

  const opsRouter = [
    { name: "APAR", path: "ops.apar" },
    { name: "Pajak Reklame", path: "ops.pajak-reklame" },
    { name: "SK BI RTGS", path: "ops.skbirtgs" },
    { name: "SK Operasional Cabang", path: "ops.sk-operasional" },
    { name: "Speciment Cabang", path: "ops.speciment" },
  ];

  const gaProcurementRouter = [
    {
      name: "KDO Mobil",
      path: "gap.kdo",
    },
    {
      name: "Toner",
      path: "gap.maintenance",
    },
    {
      name: "Alih Daya",
      path: "gap.maintenance",
    },
    {
      name: "Hasil Scoring Vendor",
      path: "gap.maintenance",
    },
    {
      name: "Biaya Perjalanan Dinas",
      path: "gap.maintenance",
    },
    {
      name: "Hasil STO",
      path: "gap.maintenance",
    },
  ];

  const infraRouter = [
    {
      name: "Izin Disnaker",
      path: "infra.disnaker",
    },
    {
      name: "Sewa Gedung",
      path: "infra.maintenance",
    },
    {
      name: "Maintenance Cost Gedung",
      path: "infra.maintenance",
    },
    {
      name: "Hasil Self Asssessment",
      path: "infra.maintenance",
    },
    {
      name: "Hasil Scoring Vendor",
      path: "infra.maintenance",
    },
  ];

  return (
    <aside
      className={`flex flex-col fixed h-screen top-0 left-0 ${
        auth.role === "cabang" ? "pt-20" : ""
      } ${
        !sidebarOpen ? "p-4 w-64 -x-translate-full" : "py-4 px-2 w-16"
      } z-5 pt-20 bg-white border-r border-gray-200 shadow-xl shadow-blue-gray-900/5`}
    >
      <List
        className={`${
          !sidebarOpen ? "min-w-[200px]" : "px-0 min-w-0 overflow-x-hidden"
        } overflow-y-auto`}
      >
        {/* Dashboard */}
        <Link href={route("dashboard")}>
          <ListItem
            className={`${sidebarOpen && "justify-center"}`}
            selected={route().current("dashboard")}
          >
            <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
              <PresentationChartBarIcon className="w-5 h-5" />
            </ListItemPrefix>
            {!sidebarOpen && (
              <Typography color="blue-gray" className={`mr-auto font-normal`}>
                Dashboard
              </Typography>
            )}
          </ListItem>
        </Link>

        {/* Inquery */}
        <hr className="my-2 border-blue-gray-50" />
        <Accordion
          open={openAcc1}
          className={`${sidebarOpen && "w-12"}`}
          icon={
            !sidebarOpen && (
              <ChevronDownIcon
                strokeWidth={2.5}
                className={`mx-auto h-4 w-4 transition-transform ${
                  openAcc1 ? "rotate-180" : ""
                }`}
              />
            )
          }
        >
          <ListItem className="p-0" selected={openAcc1}>
            <Tooltip
              content="Inquery Data"
              placement="right"
              className={`${!sidebarOpen && "hidden"}`}
            >
              <AccordionHeader
                onClick={() => {
                  handleOpenAcc(1);
                  sidebarOpen && setSidebarOpen(!sidebarOpen);
                }}
                className={`p-3 border-b-0 [&>span]:m-0 justify-center`}
              >
                <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                  <PresentationChartBarIcon className="w-5 h-5" />
                </ListItemPrefix>
                {!sidebarOpen && (
                  <Typography
                    color="blue-gray"
                    className={`mr-auto font-normal`}
                  >
                    Inquery Data
                  </Typography>
                )}
              </AccordionHeader>
            </Tooltip>
          </ListItem>
          <AccordionBody className="py-1">
            <List
              className={`p-0 ${!sidebarOpen ? "min-w-[200px]" : "min-w-0"}`}
            >
              {inqueryRouter.map((router, index) => (
                <Tooltip
                  key={index}
                  content={router.name}
                  placement="right"
                  className={`${!sidebarOpen && "hidden"}`}
                >
                  <Link href={route(router.path)}>
                    <ListItem
                      className={`${sidebarOpen && "justify-center"}`}
                      selected={route().current(router.path)}
                    >
                      <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                        <ChevronRightIcon
                          strokeWidth={3}
                          className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                        />
                      </ListItemPrefix>
                      {!sidebarOpen && <Typography>{router.name}</Typography>}
                    </ListItem>
                  </Link>
                </Tooltip>
              ))}
            </List>
          </AccordionBody>
        </Accordion>
        {/* Report */}
        <hr className="my-2 border-blue-gray-50" />
        <Accordion
          open={openAcc2}
          className={`${sidebarOpen && "w-12"}`}
          icon={
            !sidebarOpen && (
              <ChevronDownIcon
                strokeWidth={2.5}
                className={`mx-auto h-4 w-4 transition-transform ${
                  openAcc2 ? "rotate-180" : ""
                }`}
              />
            )
          }
        >
          <ListItem className="p-0" selected={openAcc2}>
            <Tooltip
              content="Report"
              placement="right"
              className={`${!sidebarOpen && "hidden"}`}
            >
              <AccordionHeader
                onClick={() => {
                  handleOpenAcc(2);
                  sidebarOpen && setSidebarOpen(!sidebarOpen);
                }}
                className={`p-3 border-b-0 [&>span]:m-0 justify-center`}
              >
                <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                  <PresentationChartBarIcon className="w-5 h-5" />
                </ListItemPrefix>
                {!sidebarOpen && (
                  <Typography
                    color="blue-gray"
                    className={`mr-auto font-normal`}
                  >
                    Report
                  </Typography>
                )}
              </AccordionHeader>
            </Tooltip>
          </ListItem>
          <AccordionBody className="py-1">
            <List
              className={`p-0 ${!sidebarOpen ? "min-w-[200px]" : "min-w-0"}`}
            >
              {reportRouter.map((router, index) => (
                <Tooltip
                  key={index}
                  content={router.name}
                  placement="right"
                  className={`${!sidebarOpen && "hidden"}`}
                >
                  <Link href={route(router.path)}>
                    <ListItem
                      className={`${sidebarOpen && "justify-center"}`}
                      selected={route().current(router.path)}
                    >
                      <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                        <ChevronRightIcon
                          strokeWidth={3}
                          className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                        />
                      </ListItemPrefix>
                      {!sidebarOpen && <Typography>{router.name}</Typography>}
                    </ListItem>
                  </Link>
                </Tooltip>
              ))}
            </List>
          </AccordionBody>
        </Accordion>

        {auth.role !== "cabang" && (
          <>
            {/* Data Maintenance */}
            <hr className="my-2 border-blue-gray-50" />
            <Accordion
              open={openAcc3}
              className={`${sidebarOpen && "w-12"}`}
              icon={
                !sidebarOpen && (
                  <ChevronDownIcon
                    strokeWidth={2.5}
                    className={`mx-auto h-4 w-4 transition-transform ${
                      openAcc3 ? "rotate-180" : ""
                    }`}
                  />
                )
              }
            >
              <ListItem className="p-0" selected={openAcc3}>
                <Tooltip
                  content="Data Maintenance"
                  placement="right"
                  className={`${!sidebarOpen && "hidden"}`}
                >
                  <AccordionHeader
                    onClick={() => {
                      handleOpenAcc(3);
                      sidebarOpen && setSidebarOpen(!sidebarOpen);
                    }}
                    className={`p-3 border-b-0 [&>span]:m-0 justify-center`}
                  >
                    <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                      <PresentationChartBarIcon className="w-5 h-5" />
                    </ListItemPrefix>
                    {!sidebarOpen && (
                      <Typography
                        color="blue-gray"
                        className={`mr-auto font-normal`}
                      >
                        Data Maintenance
                      </Typography>
                    )}
                  </AccordionHeader>
                </Tooltip>
              </ListItem>
              <AccordionBody className="py-1">
                <Accordion
                  open={openAcc4}
                  className={`${sidebarOpen && "w-12"}`}
                  icon={
                    !sidebarOpen && (
                      <ChevronDownIcon
                        strokeWidth={2.5}
                        className={`mx-auto h-4 w-4 transition-transform ${
                          openAcc4 ? "rotate-180" : ""
                        }`}
                      />
                    )
                  }
                >
                  <ListItem className="p-0" selected={openAcc4}>
                    <Tooltip
                      content="Branch OPS"
                      placement="right"
                      className={`${!sidebarOpen && "hidden"}`}
                    >
                      <AccordionHeader
                        onClick={() => {
                          handleOpenAcc(4);
                          sidebarOpen && setSidebarOpen(!sidebarOpen);
                        }}
                        className={`p-3 border-b-0 [&>span]:m-0 justify-center`}
                      >
                        <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                          <PresentationChartBarIcon className="w-5 h-5" />
                        </ListItemPrefix>
                        {!sidebarOpen && (
                          <Typography
                            color="blue-gray"
                            className={`mr-auto font-normal`}
                          >
                            Branches
                          </Typography>
                        )}
                      </AccordionHeader>
                    </Tooltip>
                  </ListItem>
                  <AccordionBody className="py-1">
                    <List
                      className={`p-0 ${
                        !sidebarOpen ? "min-w-[200px]" : "min-w-0"
                      }`}
                    >
                      <Tooltip
                        content="Data Cabang"
                        placement="right"
                        className={`${!sidebarOpen && "hidden"}`}
                      >
                        <Link href={route("branches")}>
                          <ListItem
                            className={`${sidebarOpen && "justify-center"}`}
                            selected={route().current("branches")}
                          >
                            <ListItemPrefix
                              className={`${sidebarOpen && "m-0"}`}
                            >
                              <ChevronRightIcon
                                strokeWidth={3}
                                className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                              />
                            </ListItemPrefix>
                            {!sidebarOpen && (
                              <Typography>Data Cabang</Typography>
                            )}
                          </ListItem>
                        </Link>
                      </Tooltip>
                      <Tooltip
                        content="Karyawan Cabang"
                        placement="right"
                        className={`${!sidebarOpen && "hidden"}`}
                      >
                        <Link href={route("employees")}>
                          <ListItem
                            className={`${sidebarOpen && "justify-center"}`}
                            selected={route().current("employees")}
                          >
                            <ListItemPrefix
                              className={`${sidebarOpen && "m-0"}`}
                            >
                              <ChevronRightIcon
                                strokeWidth={3}
                                className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                              />
                            </ListItemPrefix>
                            {!sidebarOpen && (
                              <Typography>Karyawan Cabang</Typography>
                            )}
                          </ListItem>
                        </Link>
                      </Tooltip>
                      {opsRouter.map((router, index) => (
                        <Tooltip
                          key={index}
                          content={router.name}
                          placement="right"
                          className={`${!sidebarOpen && "hidden"}`}
                        >
                          <Link href={route(router.path)}>
                            <ListItem
                              className={`${sidebarOpen && "justify-center"}`}
                              selected={route().current(router.path)}
                            >
                              <ListItemPrefix
                                className={`${sidebarOpen && "m-0"}`}
                              >
                                <ChevronRightIcon
                                  strokeWidth={3}
                                  className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                                />
                              </ListItemPrefix>
                              {!sidebarOpen && (
                                <Typography>{router.name}</Typography>
                              )}
                            </ListItem>
                          </Link>
                        </Tooltip>
                      ))}
                    </List>
                  </AccordionBody>
                </Accordion>
                <Accordion
                  open={openAcc5}
                  className={`${sidebarOpen && "w-12"}`}
                  icon={
                    !sidebarOpen && (
                      <ChevronDownIcon
                        strokeWidth={2.5}
                        className={`mx-auto h-4 w-4 transition-transform ${
                          openAcc5 ? "rotate-180" : ""
                        }`}
                      />
                    )
                  }
                >
                  <ListItem className="p-0" selected={openAcc5}>
                    <Tooltip
                      content="GA Procurement"
                      placement="right"
                      className={`${!sidebarOpen && "hidden"}`}
                    >
                      <AccordionHeader
                        onClick={() => {
                          handleOpenAcc(5);
                          sidebarOpen && setSidebarOpen(!sidebarOpen);
                        }}
                        className={`p-3 border-b-0 [&>span]:m-0 justify-center`}
                      >
                        <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                          <PresentationChartBarIcon className="w-5 h-5" />
                        </ListItemPrefix>
                        {!sidebarOpen && (
                          <Typography
                            color="blue-gray"
                            className={`mr-auto font-normal`}
                          >
                            GA Procurement
                          </Typography>
                        )}
                      </AccordionHeader>
                    </Tooltip>
                  </ListItem>
                  <AccordionBody className="py-1">
                    <List
                      className={`p-0 ${
                        !sidebarOpen ? "min-w-[200px]" : "min-w-0"
                      }`}
                    >
                      {gaProcurementRouter.map((router, index) => (
                        <Tooltip
                          key={index}
                          content={router.name}
                          placement="right"
                          className={`${!sidebarOpen && "hidden"}`}
                        >
                          <Link href={route(router.path)}>
                            <ListItem
                              className={`${sidebarOpen && "justify-center"}`}
                              selected={route().current(router.path)}
                            >
                              <ListItemPrefix
                                className={`${sidebarOpen && "m-0"}`}
                              >
                                <ChevronRightIcon
                                  strokeWidth={3}
                                  className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                                />
                              </ListItemPrefix>
                              {!sidebarOpen && (
                                <Typography>{router.name}</Typography>
                              )}
                            </ListItem>
                          </Link>
                        </Tooltip>
                      ))}
                    </List>
                  </AccordionBody>
                </Accordion>
                <Accordion
                  open={openAcc6}
                  className={`${sidebarOpen && "w-12"}`}
                  icon={
                    !sidebarOpen && (
                      <ChevronDownIcon
                        strokeWidth={2.5}
                        className={`mx-auto h-4 w-4 transition-transform ${
                          openAcc6 ? "rotate-180" : ""
                        }`}
                      />
                    )
                  }
                >
                  <ListItem className="p-0" selected={openAcc6}>
                    <Tooltip
                      content="GA Infra"
                      placement="right"
                      className={`${!sidebarOpen && "hidden"}`}
                    >
                      <AccordionHeader
                        onClick={() => {
                          handleOpenAcc(6);
                          sidebarOpen && setSidebarOpen(!sidebarOpen);
                        }}
                        className={`p-3 border-b-0 [&>span]:m-0 justify-center`}
                      >
                        <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                          <PresentationChartBarIcon className="w-5 h-5" />
                        </ListItemPrefix>
                        {!sidebarOpen && (
                          <Typography
                            color="blue-gray"
                            className={`mr-auto font-normal`}
                          >
                            GA Infra
                          </Typography>
                        )}
                      </AccordionHeader>
                    </Tooltip>
                  </ListItem>
                  <AccordionBody className="py-1">
                    <List
                      className={`p-0 ${
                        !sidebarOpen ? "min-w-[200px]" : "min-w-0"
                      }`}
                    >
                      {infraRouter.map((router, index) => (
                        <Tooltip
                          key={index}
                          content={router.name}
                          placement="right"
                          className={`${!sidebarOpen && "hidden"}`}
                        >
                          <Link href={route(router.path)}>
                            <ListItem
                              className={`${sidebarOpen && "justify-center"}`}
                              selected={route().current(router.path)}
                            >
                              <ListItemPrefix
                                className={`${sidebarOpen && "m-0"}`}
                              >
                                <ChevronRightIcon
                                  strokeWidth={3}
                                  className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                                />
                              </ListItemPrefix>
                              {!sidebarOpen && (
                                <Typography>{router.name}</Typography>
                              )}
                            </ListItem>
                          </Link>
                        </Tooltip>
                      ))}
                    </List>
                  </AccordionBody>
                </Accordion>
              </AccordionBody>
            </Accordion>
            {auth.role === "superadmin" && (
              <>
                {!sidebarOpen && (
                  <h2 className="text-lg font-semibold">Admin</h2>
                )}
                <hr className="my-2 border-blue-gray-50" />
                <Link href={route("uam")}>
                  <ListItem
                    className={`${sidebarOpen && "justify-center"}`}
                    selected={route().current("uam")}
                  >
                    <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                      <UserGroupIcon className="w-5 h-5" />
                    </ListItemPrefix>
                    {!sidebarOpen && (
                      <Typography
                        color="blue-gray"
                        className={`mr-auto font-normal`}
                      >
                        Users
                      </Typography>
                    )}
                  </ListItem>
                </Link>
              </>
            )}
          </>
        )}
      </List>
    </aside>
  );
}

import {
  ChartBarSquareIcon,
  ChevronDownIcon,
  ChevronRightIcon,
  CircleStackIcon,
  DocumentTextIcon,
  FolderIcon,
  PresentationChartBarIcon,
  UserGroupIcon,
} from "@heroicons/react/24/outline";
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
  const [openAcc1, setOpenAcc1] = useState(route().current("inquery.*"));
  const [openAcc2, setOpenAcc2] = useState(route().current("reporting.*"));
  const [openAcc3, setOpenAcc3] = useState(
    route().current("ops.*") ||
      route().current("gap.*") ||
      route().current("infra.*") ||
      route().current("branches") ||
      route().current("employees")
  );

  const [openAcc4, setOpenAcc4] = useState(
    route().current("ops.*") ||
      route().current("branches") ||
      route().current("employees")
  );
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

  const inqueryRouter = [
    { name: "Branch", path: "inquery.branch", state: "inquery.branch.*" },
    { name: "Staff", path: "maintenance" },
    { name: "Asset", path: "inquery.assets" },
    { name: "Lisensi", path: "inquery.licenses" },
  ];

  const reportRouter = [
    { name: "Branch", path: "reporting.branches", state: "reporting.*" },
    { name: "Asset", path: "maintenance" },
    { name: "Lisensi", path: "maintenance" },
    { name: "Vendor", path: "maintenance" },
    { name: "Branch Roll Out", path: "reporting.bros" },
  ];

  const opsRouter = [
    { name: "Data Cabang", path: "ops.branches" },
    { name: "Karyawan Cabang", path: "ops.employees" },
    { name: "APAR", path: "ops.apar" },
    { name: "Pajak Reklame", path: "ops.pajak-reklame" },
    { name: "SK BI RTGS", path: "ops.skbirtgs" },
    { name: "SK Operasional Cabang", path: "ops.sk-operasional" },
    { name: "Speciment Cabang", path: "ops.speciment" },
  ];

  const gaProcurementRouter = [
    {
      name: "Asset",
      path: "gap.assets",
    },
    {
      name: "KDO Mobil",
      path: "gap.kdos",
    },
    {
      name: "Toner",
      path: "gap.toners",
    },
    {
      name: "Alih Daya",
      path: "gap.alihdayas",
    },
    {
      name: "Perjanjian Kerjasama (PKS)",
      path: "gap.pks",
    },
    {
      name: "Hasil Scoring Vendor",
      path: "gap.scoring_projects",
    },
    {
      name: "Hasil Self Assessment",
      path: "gap.scoring_assessments",
    },
    {
      name: "Biaya Perjalanan Dinas",
      path: "gap.perdins",
    },
    {
      name: "Hasil STO",
      path: "gap.stos",
    },
  ];

  const infraRouter = [
    {
      name: "Sewa Gedung",
      path: "infra.sewa_gedungs",
    },
    {
      name: "Branch Roll Out",
      path: "infra.bros",
    },

    {
      name: "Maintenance Cost Gedung",
      path: "infra.maintenance-costs",
    },
    {
      name: "Hasil Self Asssessment",
      path: "infra.scoring_assessments",
    },
    {
      name: "Hasil Scoring Vendor",
      path: "infra.scoring_projects",
    },
    {
      name: "Izin Disnaker",
      path: "infra.disnaker",
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
              <ChartBarSquareIcon className="w-5 h-5" />
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
                      selected={
                        route().current(router.path) ||
                        route().current(`${router.path}.*`)
                      }
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
                  <DocumentTextIcon className="w-5 h-5" />
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
                      selected={route().current(router.path) ||
                        route().current(`${router.path}.*`)}
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

        {auth.role !== "branch" && (
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
                      <CircleStackIcon className="w-5 h-5" />
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
                          <FolderIcon className="w-5 h-5" />
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
                              selected={
                                route().current(router.path) ||
                                route().current(`${router.path}.*`)
                              }
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
                          <FolderIcon className="w-5 h-5" />
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
                              selected={
                                route().current(router.path) ||
                                route().current(`${router.path}.*`)
                              }
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
                          <FolderIcon className="w-5 h-5" />
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
                              selected={
                                route().current(router.path) ||
                                route().current(`${router.path}.*`)
                              }
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

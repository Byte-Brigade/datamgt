import { ChevronDownIcon, ChevronRightIcon } from "@heroicons/react/24/outline";
import {
  PresentationChartBarIcon,
  UserGroupIcon
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
  Typography
} from "@material-tailwind/react";
import { useState } from "react";

export function SidebarWithLogo({ sidebarOpen, setSidebarOpen }) {
  const [open, setOpen] = useState(1);
  const { auth } = usePage().props;
  const handleOpen = (value) => {
    setOpen(open === value ? 0 : value);
  };

  const inqueryRouter = [
    { name: "Branch", path: "inquery.branch" },
    { name: "Staff", path: "inquery.branch" },
  ];

  const reportRouter = [
    { name: "Branch", path: "reporting.branches" },
    { name: "Staff", path: "reporting.branches" },
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
      name: "Perizinan",
      path: "ga.izin",
    },
  ];

  return (
    <aside
      className={`flex flex-col fixed h-screen top-0 left-0 ${
        Array.isArray(auth.user) ? "pt-[4em]" : ""
      } z-5 ${
        !sidebarOpen ? "p-4 w-64 -x-translate-full" : "py-4 px-2 w-16"
      } pt-20 bg-white border-r border-gray-200 shadow-xl shadow-blue-gray-900/5`}
    >
      <List
        className={`${
          !sidebarOpen ? "min-w-[200px]" : "px-0 min-w-0 overflow-x-hidden"
        } overflow-y-auto`}
      >
        {/* Inquery */}
        <Accordion
          open={open === 1}
          className={`${sidebarOpen && "w-12"}`}
          icon={
            !sidebarOpen && (
              <ChevronDownIcon
                strokeWidth={2.5}
                className={`mx-auto h-4 w-4 transition-transform ${
                  open === 1 ? "rotate-180" : ""
                }`}
              />
            )
          }
        >
          <ListItem className="p-0" selected={open === 1}>
            <Tooltip
              content="Inquery Data"
              placement="right"
              className={`${!sidebarOpen && "hidden"}`}
            >
              <AccordionHeader
                onClick={() => {
                  handleOpen(1);
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
                    <ListItem className={`${sidebarOpen && "justify-center"}`}>
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
        {!Array.isArray(auth.user) && (
          <>
            {/* Report */}
            <hr className="my-2 border-blue-gray-50" />
            <Accordion
              open={open === 2}
              className={`${sidebarOpen && "w-12"}`}
              icon={
                !sidebarOpen && (
                  <ChevronDownIcon
                    strokeWidth={2.5}
                    className={`mx-auto h-4 w-4 transition-transform ${
                      open === 2 ? "rotate-180" : ""
                    }`}
                  />
                )
              }
            >
              <ListItem className="p-0" selected={open === 2}>
                <Tooltip
                  content="Report"
                  placement="right"
                  className={`${!sidebarOpen && "hidden"}`}
                >
                  <AccordionHeader
                    onClick={() => {
                      handleOpen(2);
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
                  className={`p-0 ${
                    !sidebarOpen ? "min-w-[200px]" : "min-w-0"
                  }`}
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
                        >
                          <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
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

            {/* Data Maintenance */}
            <hr className="my-2 border-blue-gray-50" />
            {!sidebarOpen && (
              <h2 className="text-lg font-semibold">Data Maintenance</h2>
            )}
            <Accordion
              open={open === 3}
              className={`${sidebarOpen && "w-12"}`}
              icon={
                !sidebarOpen && (
                  <ChevronDownIcon
                    strokeWidth={2.5}
                    className={`mx-auto h-4 w-4 transition-transform ${
                      open === 3 ? "rotate-180" : ""
                    }`}
                  />
                )
              }
            >
              <ListItem className="p-0" selected={open === 3}>
                <Tooltip
                  content="Branch OPS"
                  placement="right"
                  className={`${!sidebarOpen && "hidden"}`}
                >
                  <AccordionHeader
                    onClick={() => {
                      handleOpen(3);
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
                        <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
                          <ChevronRightIcon
                            strokeWidth={3}
                            className={`w-5 h-3 ${sidebarOpen && "my-1"}`}
                          />
                        </ListItemPrefix>
                        {!sidebarOpen && <Typography>Data Cabang</Typography>}
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
                        <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
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
                          <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
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
              open={open === 4}
              className={`${sidebarOpen && "w-12"}`}
              icon={
                !sidebarOpen && (
                  <ChevronDownIcon
                    strokeWidth={2.5}
                    className={`mx-auto h-4 w-4 transition-transform ${
                      open === 4 ? "rotate-180" : ""
                    }`}
                  />
                )
              }
            >
              <ListItem className="p-0" selected={open === 4}>
                <Tooltip
                  content="GA Procurement"
                  placement="right"
                  className={`${!sidebarOpen && "hidden"}`}
                >
                  <AccordionHeader
                    onClick={() => {
                      handleOpen(4);
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
                          <ListItemPrefix className={`${sidebarOpen && "m-0"}`}>
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
            <hr className="my-2 border-blue-gray-50" />
            {!sidebarOpen && <h2 className="text-lg font-semibold">Admin</h2>}
            {auth.role === "superadmin" && (
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
            )}
          </>
        )}
      </List>
    </aside>
  );
}

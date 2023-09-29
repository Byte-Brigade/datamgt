import { useState } from "react";
import {
  Card,
  Typography,
  List,
  ListItem,
  ListItemPrefix,
  ListItemSuffix,
  Chip,
  Accordion,
  AccordionHeader,
  AccordionBody,
  Alert,
  IconButton,
  Drawer,
} from "@material-tailwind/react";
import {
  PresentationChartBarIcon,
  ShoppingBagIcon,
  UserCircleIcon,
  Cog6ToothIcon,
  InboxIcon,
  PowerIcon,
} from "@heroicons/react/24/solid";
import {
  ChevronRightIcon,
  ChevronDownIcon,
  CubeTransparentIcon,
  XMarkIcon,
} from "@heroicons/react/24/outline";
import { Link } from "@inertiajs/react";

export function SidebarWithLogo({ sidebarOpen, setSidebarOpen }) {
  const [open, setOpen] = useState(0);
  const [collapse, setCollapse] = useState(true);
  const [openAlert, setOpenAlert] = useState(false);

  const handleOpen = (value) => {
    setOpen(open === value ? 0 : value);
  };

  return (
    <aside
      className={`flex flex-col fixed h-screen top-0 left-0 z-40 ${
        !sidebarOpen ? "p-4 w-64 -x-translate-full" : "py-4 px-2 w-16"
      } bg-white shadow-xl shadow-blue-gray-900/5`}
    >
      {/* Logo and collapse button */}
      <div
        className={`flex items-center gap-4 ${
          !sidebarOpen ? "p-4" : "py-4 justify-center"
        }`}
      >
        {!sidebarOpen && (
          <>
            {/* <img src="/img/logo-ct-dark.png" alt="brand" className="w-8 h-8" /> */}
            <Typography variant="h5" color="blue-gray">
              Sidebar
            </Typography>
          </>
        )}
        <IconButton
          variant="text"
          color="blue-gray"
          onClick={() => setSidebarOpen(!sidebarOpen)}
        >
          <svg
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
            strokeWidth={2}
            stroke="currentColor"
            className="w-5 h-5"
          >
            <path
              strokeLinecap="round"
              strokeLinejoin="round"
              d="M6 18L18 6M6 6l12 12"
            />
          </svg>
        </IconButton>
      </div>
      <List className={`${!sidebarOpen ? "min-w-[200px]" : "px-0 min-w-0"}`}>
        <Accordion
          open={open === 1}
          className={`${!sidebarOpen ? "" : "w-12"}`}
          icon={
            <ChevronDownIcon
              strokeWidth={2.5}
              className={`mx-auto h-4 w-4 transition-transform ${
                open === 1 ? "rotate-180" : ""
              } ${!sidebarOpen ? "" : "hidden"}`}
            />
          }
        >
          <ListItem className="p-0" selected={open === 1}>
            <AccordionHeader
              onClick={() => handleOpen(1)}
              className={`p-3 border-b-0`}
            >
              <ListItemPrefix>
                <PresentationChartBarIcon className="w-5 h-5" />
              </ListItemPrefix>
              {!sidebarOpen && (
                <Typography color="blue-gray" className="mr-auto font-normal">
                  Branch OPS
                </Typography>
              )}
            </AccordionHeader>
          </ListItem>
          <AccordionBody className="py-1">
            <List className="p-0">
              <Link href={route("branches")}>
                <ListItem>
                  <ListItemPrefix>
                    <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                  </ListItemPrefix>
                  Data Cabang
                </ListItem>
              </Link>
              <Link href={route("employees")}>
                <ListItem>
                  <ListItemPrefix>
                    <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                  </ListItemPrefix>
                  Karyawan Cabang
                </ListItem>
              </Link>
              <Link href={route("ops.apar")}>
                <ListItem>
                  <ListItemPrefix>
                    <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                  </ListItemPrefix>
                  APAR
                </ListItem>
              </Link>
              <Link href={route("ops.pajak-reklame")}>
                <ListItem>
                  <ListItemPrefix>
                    <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                  </ListItemPrefix>
                  Pajak Reklame
                </ListItem>
              </Link>
              <Link href={route("ops.skbirtgs")}>
                <ListItem>
                  <ListItemPrefix>
                    <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                  </ListItemPrefix>
                  SK BI RTGS
                </ListItem>
              </Link>
              <Link href={route("ops.sk-operasional")}>
                <ListItem>
                  <ListItemPrefix>
                    <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                  </ListItemPrefix>
                  SK Operasional Cabang
                </ListItem>
              </Link>
              <Link href={route("ops.speciment")}>
                <ListItem>
                  <ListItemPrefix>
                    <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                  </ListItemPrefix>
                  Speciment
                </ListItem>
              </Link>
            </List>
          </AccordionBody>
        </Accordion>
        <Accordion
          open={open === 2}
          className={`${!sidebarOpen ? "" : "w-12"}`}
          icon={
            <ChevronDownIcon
              strokeWidth={2.5}
              className={`mx-auto h-4 w-4 transition-transform ${
                open === 2 ? "rotate-180" : ""
              }`}
            />
          }
        >
          <ListItem className="p-0" selected={open === 2}>
            <AccordionHeader
              onClick={() => handleOpen(2)}
              className="p-3 border-b-0"
            >
              <ListItemPrefix>
                <ShoppingBagIcon className="w-5 h-5" />
              </ListItemPrefix>
              {!sidebarOpen && (
                <Typography color="blue-gray" className="mr-auto font-normal">
                  E-Commerce
                </Typography>
              )}
            </AccordionHeader>
          </ListItem>
          <AccordionBody className="py-1">
            <List className="p-0">
              <ListItem>
                <ListItemPrefix>
                  <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                </ListItemPrefix>
                Orders
              </ListItem>
              <ListItem>
                <ListItemPrefix>
                  <ChevronRightIcon strokeWidth={3} className="w-5 h-3" />
                </ListItemPrefix>
                Products
              </ListItem>
            </List>
          </AccordionBody>
        </Accordion>
        <hr className="my-2 border-blue-gray-50" />
        {/* <ListItem>
          <ListItemPrefix>
            <InboxIcon className="w-5 h-5" />
          </ListItemPrefix>
          Inbox
          <ListItemSuffix>
            <Chip
              value="14"
              size="sm"
              variant="ghost"
              color="blue-gray"
              className="rounded-full"
            />
          </ListItemSuffix>
        </ListItem>
        <ListItem>
          <ListItemPrefix>
            <UserCircleIcon className="w-5 h-5" />
          </ListItemPrefix>
          Profile
        </ListItem>
        <ListItem>
          <ListItemPrefix>
            <Cog6ToothIcon className="w-5 h-5" />
          </ListItemPrefix>
          Settings
        </ListItem>
        <ListItem>
          <ListItemPrefix>
            <PowerIcon className="w-5 h-5" />
          </ListItemPrefix>
          Log Out
        </ListItem> */}
      </List>
      <Alert
        open={openAlert}
        className="mt-auto"
        onClose={() => setOpenAlert(false)}
      >
        <CubeTransparentIcon className="w-12 h-12 mb-4" />
        <Typography variant="h6" className="mb-1">
          Upgrade to PRO
        </Typography>
        <Typography variant="small" className="font-normal opacity-80">
          Upgrade to Material Tailwind PRO and get even more components,
          plugins, advanced features and premium.
        </Typography>
        <div className="flex gap-3 mt-4">
          <Typography
            as="a"
            href="#"
            variant="small"
            className="font-medium opacity-80"
            onClick={() => setOpenAlert(false)}
          >
            Dismiss
          </Typography>
          <Typography as="a" href="#" variant="small" className="font-medium">
            Upgrade Now
          </Typography>
        </div>
      </Alert>
    </aside>
  );
}

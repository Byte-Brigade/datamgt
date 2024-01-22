import { DateTime } from "@/Utils/DateTime";
import {
  Bars3Icon,
  ChevronDownIcon,
  UserCircleIcon,
} from "@heroicons/react/24/outline";
import { ArrowLeftOnRectangleIcon } from "@heroicons/react/24/solid";
import { Link, usePage } from "@inertiajs/react";
import {
  Button,
  IconButton,
  Menu,
  MenuHandler,
  MenuItem,
  MenuList,
  Navbar,
  Typography,
} from "@material-tailwind/react";
import axios from "axios";
import { createElement, useState } from "react";

const logout = () => {
  const token = localStorage.getItem("auth");
  const headers = {
    Authorization: `Bearer ${token}`,
  };
  axios
    .post(
      route("api/logout"),
      {},
      {
        headers: headers,
      }
    )
    .then(() => localStorage.removeItem("auth"));
};

// profile menu component
const profileMenuItems = [
  {
    link: route("profile.edit"),
    label: (
      <Link href={route("profile.edit")}>
        <Typography>My Profile</Typography>
      </Link>
    ),
    icon: UserCircleIcon,
  },
  {
    label: (
      <Link href={route("logout")} method="post" as="ul" onClick={logout}>
        <Typography>Keluar</Typography>
      </Link>
    ),
    icon: ArrowLeftOnRectangleIcon,
  },
];

function ProfileMenu({ auth }) {
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const closeMenu = () => setIsMenuOpen(false);
  return (
    <Menu open={isMenuOpen} handler={setIsMenuOpen} placement="bottom-end">
      <MenuHandler>
        <Button
          variant="text"
          color="blue-gray"
          className="flex items-center gap-1 rounded-full py-0.5 pr-2 pl-0.5 lg:ml-auto"
        >
          <UserCircleIcon className="w-8 h-8" />
          <span className="text-sm capitalize text-slate-700">
            {auth.user.name} | {auth.user.roles[0].alt_name}
          </span>
          <ChevronDownIcon
            strokeWidth={2.5}
            className={`h-3 w-3 transition-transform ${
              isMenuOpen ? "rotate-180" : ""
            }`}
          />
        </Button>
      </MenuHandler>
      <MenuList className="p-1">
        {profileMenuItems.map(({ label, icon, link }, key) => {
          const isLastItem = key === profileMenuItems.length - 1;
          return (
            <MenuItem
              key={key}
              onClick={closeMenu}
              href={link}
              className={`flex items-center gap-2 rounded ${
                isLastItem
                  ? "hover:bg-red-500/10 focus:bg-red-500/10 active:bg-red-500/10"
                  : ""
              }`}
            >
              {createElement(icon, {
                className: `h-4 w-4 ${isLastItem ? "text-red-500" : ""}`,
                strokeWidth: 2,
              })}
              <Typography
                as="span"
                variant="small"
                className="font-normal"
                color={isLastItem ? "red" : "inherit"}
              >
                {label}
              </Typography>
            </MenuItem>
          );
        })}
      </MenuList>
    </Menu>
  );
}
export function ComplexNavbar({ sidebarOpen, setSidebarOpen }) {
  const { auth } = usePage().props;

  return (
    <Navbar
      blurred={false}
      shadow={false}
      className="fixed top-0 z-50 max-w-full px-2 py-1 border-b border-gray-200 rounded-none h-max"
    >
      <div className="relative flex items-center mx-auto text-blue-gray-900">
        {auth.role !== "cabang" ? (
          <div className="flex items-center justify-between w-full gap-4 px-2 py-2">
            <IconButton
              variant="text"
              color="blue-gray"
              onClick={() => setSidebarOpen(!sidebarOpen)}
            >
              <Bars3Icon className="w-5 h-5" />
            </IconButton>
            <ProfileMenu auth={auth} />
          </div>
        ) : (
          <div className="flex items-center justify-between w-full px-2 py-2">
            <div className="flex items-center gap-x-4">
              <IconButton
                variant="text"
                color="blue-gray"
                onClick={() => setSidebarOpen(!sidebarOpen)}
              >
                <Bars3Icon className="w-5 h-5" />
              </IconButton>
              <div className="flex flex-col">
                <Typography className="font-semibold">
                  BRANCH OPERATION MANAGEMENT
                </Typography>
                <Typography className="text-sm font-light">
                  {DateTime()}
                </Typography>
              </div>
            </div>
            <ProfileMenu auth={auth} />
          </div>
        )}
      </div>
    </Navbar>
  );
}

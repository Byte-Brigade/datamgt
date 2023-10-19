import { DateTime } from "@/Utils/DateTime";
import {
  Bars2Icon,
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
import React from "react";
import { createElement } from "react";
import { useState } from "react";

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
      <Link href={route("logout")} method="post" as="div">
        <Typography>Sign Out</Typography>
      </Link>
    ),
    icon: ArrowLeftOnRectangleIcon,
  },
];

function ProfileMenu() {
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
  const [isNavOpen, setIsNavOpen] = React.useState(false);

  const toggleIsNavOpen = () => setIsNavOpen((cur) => !cur);
  const { auth } = usePage().props;

  return (
    <Navbar
      blurred={false}
      shadow={false}
      className="fixed top-0 z-10 max-w-full px-2 py-1 border-b border-gray-200 rounded-none h-max lg:px-0 lg:py-1"
    >
      <div className="relative flex items-center mx-auto text-blue-gray-900">
        {!Array.isArray(auth.user) ? (
          <div className="flex items-center justify-between w-full gap-4 px-4 py-2">
            <IconButton
              variant="text"
              color="blue-gray"
              onClick={() => setSidebarOpen(!sidebarOpen)}
            >
              <Bars3Icon className="w-5 h-5" />
            </IconButton>
            <IconButton
              size="sm"
              color="blue-gray"
              variant="text"
              onClick={toggleIsNavOpen}
              className="block w-full ml-auto mr-2 lg:hidden"
            >
              <Bars2Icon className="w-6 h-6" />
            </IconButton>
            <ProfileMenu />
          </div>
        ) : (
          <div className="flex flex-col items-center w-full gap-4 px-4 py-2">
            <div className="flex justify-between w-full">
              <Typography
                as="a"
                onClick={() => setSidebarOpen(!sidebarOpen)}
                className="mr-4 cursor-pointer py-1.5 font-medium"
              >
                BRANCH OPERATION MANAGEMENT
              </Typography>
            </div>
            <div className="flex justify-between w-full">
              <Typography
                as="a"
                onClick={() => setSidebarOpen(!sidebarOpen)}
                className="mr-4 cursor-pointer py-1.5 font-medium"
              >
                {DateTime()}
              </Typography>
              <a href={route("login")}>
                <Button>Login</Button>
              </a>
            </div>
          </div>
        )}
      </div>
    </Navbar>
  );
}

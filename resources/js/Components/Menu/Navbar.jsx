import {
  Bars3Icon,
  ChevronDownIcon,
  UserCircleIcon
} from "@heroicons/react/24/outline";
import { ArrowLeftOnRectangleIcon } from "@heroicons/react/24/solid";
import { Link } from "@inertiajs/react";
import {
  Button,
  IconButton,
  Menu,
  MenuHandler,
  MenuItem,
  MenuList,
  Navbar,
  Typography
} from "@material-tailwind/react";
import React from "react";

// profile menu component
const profileMenuItems = [
  {
    label: (
      <Link href={route("profile.edit")}>
        <Typography>My Profile</Typography>
      </Link>
    ),
    icon: UserCircleIcon,
  },
  {
    label: (
      <Link href={route("logout")} method="post">
        <Typography>Keluar</Typography>
      </Link>
    ),
    icon: ArrowLeftOnRectangleIcon,
  },
];

function ProfileMenu() {
  const [isMenuOpen, setIsMenuOpen] = React.useState(false);

  const closeMenu = () => setIsMenuOpen(false);

  return (
    <Menu open={isMenuOpen} handler={setIsMenuOpen} placement="bottom-end">
      <MenuHandler>
        <Button
          variant="text"
          color="blue-gray"
          className="flex items-center gap-1 rounded-full py-0.5 pr-2 pl-0.5 lg:ml-auto"
        >
          <UserCircleIcon className="w-10 h-10" />
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
              key={label}
              onClick={closeMenu}
              href={link}
              className={`flex items-center gap-2 rounded ${
                isLastItem
                  ? "hover:bg-red-500/10 focus:bg-red-500/10 active:bg-red-500/10"
                  : ""
              }`}
            >
              {React.createElement(icon, {
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
  return (
    <Navbar className="sticky top-0 z-10 max-w-full rounded-none shadow-none h-max lg:px-0 lg:py-1">
      <div className="relative flex items-center mx-auto text-blue-gray-900">
        <div className="flex items-center justify-between w-full gap-4 px-4 py-2">
          <IconButton
            variant="text"
            color="blue-gray"
            onClick={() => setSidebarOpen(!sidebarOpen)}
          >
            <Bars3Icon className="w-5 h-5" />
          </IconButton>
          <ProfileMenu />
        </div>
      </div>
    </Navbar>
  );
}

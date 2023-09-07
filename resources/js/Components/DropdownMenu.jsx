import {
  Menu,
  MenuHandler,
  MenuList,
  MenuItem,
  Button,
  Typography,
} from "@material-tailwind/react";

import {
  PencilIcon,
  TrashIcon,
  Cog6ToothIcon,
} from "@heroicons/react/24/solid";

export default function DropdownMenu({
  placement,
  onEditClick,
  onDeleteClick,
}) {
  return (
    <Menu
      animate={{
        mount: { x: 0 },
        unmount: { x: 25 },
      }}
      placement={placement}
    >
      <MenuHandler>
        <Button className="flex items-center gap-x-1" size="sm" color="indigo">
          <Cog6ToothIcon className="w-4 h-4" />
          Option
        </Button>
      </MenuHandler>
      <MenuList>
        <MenuItem className="flex items-center gap-x-2" onClick={onEditClick}>
          <PencilIcon className="w-4 h-4" />
          <Typography variant="small" color="gray" className="font-normal">
            Edit
          </Typography>
        </MenuItem>
        <MenuItem className="flex items-center gap-x-2" onClick={onDeleteClick}>
          <TrashIcon className="w-4 h-4 text-red-500" />
          <Typography variant="small" color="red" className="font-normal">
            Delete
          </Typography>
        </MenuItem>
      </MenuList>
    </Menu>
  );
}

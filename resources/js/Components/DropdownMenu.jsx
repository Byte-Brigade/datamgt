import {
  IconButton,
  Menu,
  MenuHandler,
  MenuItem,
  MenuList,
  Tooltip,
  Typography,
} from "@material-tailwind/react";

import { PencilIcon, TrashIcon } from "@heroicons/react/24/solid";
import { usePage } from "@inertiajs/react";
import { WrenchScrewdriverIcon } from "@heroicons/react/24/outline";

export default function DropdownMenu({
  placement,
  onEditClick,
  onDeleteClick,
}) {
  const { auth } = usePage().props;
  return (
    <Menu
      animate={{
        mount: { x: 0 },
        unmount: { x: 25 },
      }}
      placement={placement}
    >
      <Tooltip content="Options">
        <MenuHandler>
          <IconButton size="sm" variant="text">
            <WrenchScrewdriverIcon className="w-4 h-4" />
          </IconButton>
        </MenuHandler>
      </Tooltip>
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

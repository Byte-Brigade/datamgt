import PrimaryButton from "@/Components/PrimaryButton";
import SecondaryButton from "@/Components/SecondaryButton";
import {
  Button,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
} from "@material-tailwind/react";
import { XMarkIcon } from "@heroicons/react/24/solid";
export default function Modal({
  children,
  // show = false,
  // maxWidth = "2xl",
  // closeable = true,
  // onClose = () => {},
  isOpen,
  onSubmit,
  onToggle,
  name,
  isProcessing = false
}) {

  // const close = () => {
  //   if (closeable) {
  //     onClose();
  //   }
  // };

  // const maxWidthClass = {
  //   sm: "sm:max-w-sm",
  //   md: "sm:max-w-md",
  //   lg: "sm:max-w-lg",
  //   xl: "sm:max-w-xl",
  //   "2xl": "sm:max-w-2xl",
  // }[maxWidth];

  return (
    <Dialog open={isOpen} handler={onToggle} size="md">
        <DialogHeader className="flex items-center justify-between">
          {name}
          <IconButton
            size="sm"
            variant="text"
            className="p-2"
            color="gray"
            onClick={onToggle}
          >
            <XMarkIcon className="w-6 h-6" />
          </IconButton>
        </DialogHeader>
        <DialogBody divider>
            {children}
        </DialogBody>
        <DialogFooter>
          <div className="flex flex-row-reverse gap-x-4">
            <Button
              onClick={onSubmit}
              disabled={isProcessing}
              type="submit"
            >
              Buat
            </Button>
            <SecondaryButton type="button" onClick={onToggle}>
              Tutup
            </SecondaryButton>
          </div>
        </DialogFooter>
      </Dialog>
  );
}

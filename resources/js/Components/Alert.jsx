import { XMarkIcon } from "@heroicons/react/24/outline";
import { IconButton } from "@material-tailwind/react";
import { useState } from "react";

export default function Alert({ sessions }) {
  const [close, setClose] = useState(false);
  const onCloseAlert = () => {
    setClose((prev) => !prev);
  };

  const status =
    sessions.status === "success"
      ? "Berhasil"
      : sessions.status === "failed"
      ? "Gagal"
      : sessions.status;
  return (
    <div
      className={`border-l-4 ${
        sessions.status === "success"
          ? "bg-green-100 border-green-500 text-green-700"
          : "bg-orange-100 border-orange-500 text-orange-700"
      } p-4 mb-4 ${close ? "hidden" : ""}`}
      role="alert"
    >
      <div className="flex items-center justify-between">
        <div className="flex flex-col">
          <p className="font-bold">{status}</p>
          <p>{sessions.message}</p>
        </div>
        <IconButton onClick={onCloseAlert} variant="text">
          <XMarkIcon className="w-5 h-5" />
        </IconButton>
      </div>
    </div>
  );
}

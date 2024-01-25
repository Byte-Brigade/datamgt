import { SignalIcon, SignalSlashIcon } from "@heroicons/react/24/outline";

export default function CheckStatus({ status }) {
  if (status === "Offline") {
    return (
      <div className="flex items-center mt-2 gap-x-2">
        <SignalSlashIcon className="w-4 h-4 text-red-500" />
        <p className="text-sm text-red-500">{status}</p>
      </div>
    );
  }
  return (
    <div className="flex items-center mt-2 gap-x-2">
      <SignalIcon className="w-4 h-4 text-green-500" />
      <p className="text-sm text-green-500">{status}</p>
    </div>
  );
}

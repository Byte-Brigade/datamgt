import { SignalIcon, SignalSlashIcon } from "@heroicons/react/24/outline";

export default function CheckStatus({ status, loading }) {
  if (loading) {
    return (
      <div className="flex items-center mt-2 gap-x-2 animate-pulse">
        <SignalIcon className="w-4 h-4 text-slate-300" />
        <div className="py-1 px-9 bg-slate-300 rounded-xl"></div>
      </div>
    );
  }

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

import { Link } from "@inertiajs/react";

export default function Guest({ children }) {
  return (
    <div className="grid min-h-screen grid-cols-2 bg-gray-100 place-items-center">
      <div className="flex flex-col items-center justify-center p-28">
        <Link href="/">
          <img className="size-64" src="/logo.png" />
        </Link>
        <h1 className="mt-4 text-4xl font-semibold text-center">
          Pengelolaan Data-data Branch Operation Management
        </h1>
      </div>

      <div className="flex flex-col items-center justify-center w-full h-full overflow-hidden bg-white shadow-md">
        {children}
      </div>
    </div>
  );
}

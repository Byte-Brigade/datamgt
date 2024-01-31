import ApplicationLogo from "@/Components/ApplicationLogo";
import { Link } from "@inertiajs/react";

export default function Guest({ children }) {
  return (
    <div className="grid min-h-screen grid-cols-2 bg-gray-100 place-items-center">
      <div className="flex flex-col items-center justify-center p-28">
        <Link href="/">
          <ApplicationLogo className="w-48 h-48 text-gray-500 fill-current" />
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

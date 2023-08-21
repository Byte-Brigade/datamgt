import { useState } from "react";
import Sidebar from "@/Components/Sidebar";

export default function Authenticated({ auth, header, children }) {
  const [showingNavigationDropdown, setShowingNavigationDropdown] =
    useState(false);

  return (
    <div className="min-h-screen bg-gray-100">
      {/* {header && (
        <header className="bg-white shadow">
          <div className="px-4 py-6 mx-auto max-w-7xl sm:px-6 lg:px-8">
            {header}
          </div>
        </header>
      )} */}

      <Sidebar />
      <main className="p-4 sm:ml-64">{children}</main>
    </div>
  );
}

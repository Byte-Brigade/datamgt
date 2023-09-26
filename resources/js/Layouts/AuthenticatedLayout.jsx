import { useState } from "react";
import Sidebar from "@/Components/Sidebar";
import { SidebarWithLogo } from "@/Components/Menu/Sidebar";

export default function Authenticated({ auth, header, children }) {
  const [sidebarOpen, setSidebarOpen] =
    useState(true);

  return (
    <div className={`w-full grid min-h-screen ${!sidebarOpen ? 'grid-cols-sidebarCollapsed' : 'grid-cols-sidebar' } transition-[grid-template-columns] duration-300 ease-in-out bg-gray-100`}>

      <SidebarWithLogo sidebarOpen={sidebarOpen} setSidebarOpen={setSidebarOpen} />
      <main className="p-4">{children}</main>
    </div>
  );
}

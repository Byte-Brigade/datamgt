import { useState } from "react";
import Sidebar from "@/Components/Sidebar";
import { SidebarWithLogo } from "@/Components/Menu/Sidebar";

export default function Authenticated({ auth, header, children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div
      className={`grid min-h-screen ${
        !sidebarOpen ? "grid-cols-sidebar" : "grid-cols-sidebarCollapse"
      } transition-[grid-template-columns] duration-300 ease-in-out bg-gray-100`}
    >
      <SidebarWithLogo
        sidebarOpen={sidebarOpen}
        setSidebarOpen={setSidebarOpen}
      />
      <main>{children}</main>
    </div>
  );
}

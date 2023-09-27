import { useState } from "react";
import Sidebar from "@/Components/Sidebar";
import { SidebarWithLogo } from "@/Components/Menu/Sidebar";

export default function Authenticated({ auth, header, children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);

  return (
    <div className={`min-h-screen bg-gray-100`}>
      <SidebarWithLogo
        sidebarOpen={sidebarOpen}
        setSidebarOpen={setSidebarOpen}
      />
      <main
        className={`p-4 ${
          sidebarOpen ? "ml-16" : "ml-64"
        } transition-[margin] duration-150 ease-in-out`}
      >
        {children}
      </main>
    </div>
  );
}

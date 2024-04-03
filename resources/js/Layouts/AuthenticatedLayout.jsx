import { SidebarWithLogo } from "@/Components/Menu/Sidebar";
import { ComplexNavbar } from "@/Components/Menu/Navbar";
import { useState } from "react";

export default function Authenticated({ auth, header, children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  return (
    <div className={`min-h-screen bg-gray-100`}>
      <ComplexNavbar
        sidebarOpen={sidebarOpen}
        setSidebarOpen={setSidebarOpen}
      />
      <SidebarWithLogo
        sidebarOpen={sidebarOpen}
        setSidebarOpen={setSidebarOpen}
        role={auth.user}
      />
      <main
        className={`p-4 ${
          sidebarOpen ? "ml-16" : "ml-64"
        } transition-[margin] duration-150 ease-in-out`}
      >
        <div className={Array.isArray(auth.user) ? "mt-28" : "mt-16"}>
          {children}
        </div>
      </main>
    </div>
  );
}

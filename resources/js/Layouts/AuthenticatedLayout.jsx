import { SidebarWithLogo } from "@/Components/Menu/Sidebar";
import { ComplexNavbar } from "@/Components/Menu/Navbar";
import { useState } from "react";
import Sidebar from "@/Components/Sidebar";

export default function Authenticated({ auth, header, children }) {
  const [sidebarOpen, setSidebarOpen] = useState(false);
  return (
    <div className={`min-h-screen bg-gray-100`}>
      {/* <nav className="fixed top-0 left-0 z-50 w-full bg-white border-b border-slate-200">
        <div className="flex flex-wrap items-center max-w-screen-xl p-4 mx-auto justify between">
          <h2 className="text-xl font-semibold">Navbar</h2>
        </div>
      </nav> */}
      <div className="relative">
        <ComplexNavbar
          sidebarOpen={sidebarOpen}
          setSidebarOpen={setSidebarOpen}
        />
        <SidebarWithLogo
          sidebarOpen={sidebarOpen}
          setSidebarOpen={setSidebarOpen}
          role={auth.user}
        />
      </div>
      {/* <Sidebar /> */}
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

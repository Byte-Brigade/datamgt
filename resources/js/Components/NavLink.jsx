import { Link } from "@inertiajs/react";

export default function NavLink({
  active = false,
  className = "",
  children,
  ...props
}) {
  return (
    <Link
      {...props}
      className={
        "flex items-center p-2 text-gray-900 rounded-lg dark:text-white " +
        (active
          ? "border-blue-400 text-gray-900 focus:border-blue-700 bg-slate-200 "
          : "border-transparent text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-700 focus:text-gray-700 focus:border-gray-300 ") +
        className
      }
    >
      {children}
    </Link>
  );
}

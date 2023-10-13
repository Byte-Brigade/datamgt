import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link } from "@inertiajs/react";
import React from "react";

export default function Branch({ sessions, branches, auth }) {
  console.log(branches);
  const { data } = branches;
  const columns = [
    { name: "Name" },
    { name: "Tipe" },
    { name: "Alamat" },
    { name: "BM" },
  ];

  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Branch" />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div className="relative overflow-x-auto border-2 rounded-lg border-slate-200">
            <table className={`text-sm leading-3 w-full`}>
              <thead className="border-b-2 border-slate-200">
                <tr className="[&>th]:p-2 bg-slate-100">
                  <th className="text-center">No</th>
                  {columns.map((column, index) => (
                    <th key={column.name}>
                      <div>{column.name}</div>
                    </th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {data.map((branch, index) => (
                  <tr
                    key={index}
                    className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200"
                  >
                    <td>{index + 1}</td>
                    <td>
                      <Link
                        className="text-blue-500"
                        href={route(
                          "inquery.branch.detail",
                          branch.branch_code
                        )}
                      >
                        {branch.branch_name}
                      </Link>
                    </td>
                    <td>{branch.branch_types.type_name}</td>
                    <td>{branch.address}</td>
                    <td>{branch.employees[0].name}</td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

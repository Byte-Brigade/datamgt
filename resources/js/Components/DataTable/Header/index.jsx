import React from "react";

export default function Header({ headers }) {
  return (
    <thead className="border-b-2 border-slate-200">
      <tr className="[&>th]:p-2 bg-slate-100">
        {headers.map((header, index) => (
          <th key={index} name={header.field}>
            {header.name}
          </th>
        ))}
      </tr>
    </thead>
  );
}

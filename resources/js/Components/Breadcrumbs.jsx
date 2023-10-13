import { HomeIcon } from "@heroicons/react/24/solid";
import { Breadcrumbs } from "@material-tailwind/react";
import React from "react";

export default function BreadcrumbsWithLogo({ crumbs }) {
  return (
    <Breadcrumbs>
      <a href="#" className="opacity-60">
        <HomeIcon className="w-2 h-2" />
      </a>
      {crumbs.map((crumb, i) => (
        <a key={i} href="#" className="opacity-60">
          <span>{crumb.name}</span>
        </a>
      ))}
    </Breadcrumbs>
  );
}

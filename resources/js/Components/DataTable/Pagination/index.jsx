import { Link, usePage } from "@inertiajs/react";
import React from "react";

export default function Pagination({ data, url }) {
  return (
    <div className="flex items-center justify-between mt-4">
      <div>
        Showing {data.from} to {data.to} of {data.total} entries
      </div>
      <div className="flex items-center gap-2">
        {!data.first_page_url.includes(url) && (
          <Link
            href={data.first_page_url}
            className="p-2 text-sm rounded-lg bg-slate-100"
            preserveScroll
            preserveState
          >
            <div>First</div>
          </Link>
        )}
        {data.links.map(
          (link, index) =>
            link.url && (
              <Link
                key={index}
                href={link.url}
                className={`${
                  link.url.includes(url) ? `bg-slate-200` : `bg-slate-100`
                } py-2 px-3 text-sm rounded-lg`}
                preserveScroll
                preserveState
              >
                <div dangerouslySetInnerHTML={{ __html: link.label }}></div>
              </Link>
            )
        )}
        {!data.last_page_url.includes(url) && (
          <Link
            href={data.last_page_url}
            className="p-2 text-sm rounded-lg bg-slate-100"
            preserveScroll
            preserveState
          >
            <div>Last</div>
          </Link>
        )}
      </div>
    </div>
  );
}

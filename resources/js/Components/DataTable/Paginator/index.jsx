import { useEffect, useState } from "react";

const OFFSET = 2;

export default function Paginator({ pagination, pageChanged, totalItems }) {
  const [pageNumbers, setPageNumbers] = useState([]);

  useEffect(() => {
    const setPaginationPages = () => {
      let pages = [];
      const { last_page, current_page, from, to } = pagination;
      if (!to) return [];
      let fromPage = current_page - OFFSET;
      if (fromPage < 1) fromPage = 1;
      let toPage = current_page + OFFSET;
      if (toPage >= last_page) toPage = last_page;
      for (let page = fromPage; page <= toPage; page++) {
        pages.push(page);
      }
      setPageNumbers(pages);
    };
    setPaginationPages();
  }, [pagination]);

  return (
    <div className="flex items-center justify-between mt-4">
      <div className="[&>span]:font-bold">
        Showing <span>{pagination.from}</span> to <span>{pagination.to}</span>{" "}
        of <span>{pagination.total}</span> entries
      </div>
      <div className="flex items-center gap-2">
        {pagination.current_page !== 1 && (
          <>
            <button
              className="p-2 text-sm transition duration-150 ease-in-out rounded-lg cursor-pointer hover:bg-slate-300 focus:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-slate-100"
              onClick={() => pageChanged(1)}
            >
              <div>First</div>
            </button>
            <button
              className="p-2 text-sm transition duration-150 ease-in-out rounded-lg cursor-pointer hover:bg-slate-300 focus:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-slate-100"
              onClick={() => pageChanged(pagination.current_page - 1)}
            >
              <div>Previous</div>
            </button>
          </>
        )}
        {pageNumbers.map((pageNumber, index) => (
          <button
            key={index}
            className={`${
              pageNumber === pagination.current_page
                ? `bg-slate-200`
                : `bg-slate-100`
            } py-2 px-3 text-sm rounded-lg cursor-pointer hover:bg-slate-300 focus:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150`}
            onClick={() => pageChanged(pageNumber)}
          >
            <div>{pageNumber}</div>
          </button>
        ))}
        {pagination.current_page !== pagination.last_page && (
          <>
            <button
              className="p-2 text-sm transition duration-150 ease-in-out rounded-lg cursor-pointer hover:bg-slate-300 focus:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-slate-100"
              onClick={() => pageChanged(pagination.current_page + 1)}
            >
              <div>Next</div>
            </button>
            <button
              className="p-2 text-sm transition duration-150 ease-in-out rounded-lg cursor-pointer hover:bg-slate-300 focus:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 bg-slate-100"
              onClick={() => pageChanged(pagination.last_page)}
            >
              <div>Last</div>
            </button>
          </>
        )}
      </div>
    </div>
  );
}

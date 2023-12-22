import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, usePage } from "@inertiajs/react";

export default function Page({ sessions, auth, data }) {
  const { url } = usePage();

  const groupBy = (array, key) =>
    array.reduce((result, item) => {
      // Extract the value for the current key
      const keyValue = item[key];

      // If the key doesn't exist in the result object, create it with an empty array
      if (!result[keyValue]) {
        result[keyValue] = [];
      }

      // Push the current item to the array associated with the key
      result[keyValue].push(item);

      return result;
    }, {});


  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Branch" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <table className={`text-sm leading-3 bg-white mt-2`}>
            <thead className="sticky border-b-2 table-fixed top-16 border-slate-200">
              <tr className="[&>th]:p-2 bg-slate-100 border border-slate-200 divide-x divide-slate-200">
                <th
                  className="text-center border-r border-slate-200"
                  rowSpan={3}
                  colSpan={2}
                >
                  Scoring Schedule
                </th>
                <th className="text-center" rowSpan={3} colSpan={2}>
                  Jumlah Vendor
                </th>
                <th className="text-center" colSpan={7}>
                  Type Scoring
                </th>

                {/* Lokasi: Kantor Pusat, Cabang */}
                {/* Kategori A (Asset Depresiasi) */}
                {/* Kategori A (Asset Non-Depresiasi) */}
              </tr>
              <tr className="[&>th]:p-2 bg-slate-100 border border-slate-200 divide-x divide-slate-200">
                <th className="text-center" colSpan={2}>
                  Assessment (PKS)
                </th>
                <th className="text-center" colSpan={2}>
                  Project (Non PKS)
                </th>
                <th className="text-center" colSpan={3}>
                  SLA
                </th>
              </tr>
              <tr className="[&>th]:p-2 bg-slate-100 border border-slate-200 divide-x divide-slate-200">
                <th className="text-center">Done</th>
                <th className="text-center">On Progress</th>
                <th className="text-center">Done</th>
                <th className="text-center">On Progress</th>
                <th className="text-center">YES</th>
                <th className="text-center">NO</th>
                <th className="text-center">On Progress</th>
              </tr>
            </thead>
            <tbody className="overflow-y-auto">
              {Object.entries(
                groupBy(data.gap_scorings, "schedule_scoring")
              ).map(([key, scoring]) => (
                <tr className="[&>td]:p-2 hover:bg-slate-200 border-b divide-x divide-slate-200 border-slate-200">
                  <td colSpan={2} className="text-center">
                    {key}
                  </td>
                  <td className="text-center" colSpan={2}>
                    {scoring.length}
                  </td>

                  <td className="text-center">
                    {
                      scoring.filter(
                        (item) =>
                          item.status_pekerjaan === "Done" &&
                          item.type === "Assessment"
                      ).length
                    }
                  </td>
                  <td className="text-center">
                    {
                      scoring.filter(
                        (item) =>
                          item.status_pekerjaan === "On Progress" &&
                          item.type === "Assessment"
                      ).length
                    }
                  </td>

                  <td className="text-center">
                    {
                      scoring.filter(
                        (item) =>
                          item.status_pekerjaan === "Done" &&
                          item.type === "Project"
                      ).length
                    }
                  </td>
                  <td className="text-center">
                    {
                      scoring.filter(
                        (item) =>
                          item.status_pekerjaan === "On Progress" &&
                          item.type === "Project"
                      ).length
                    }
                  </td>
                  <td className="text-center">
                    {
                      scoring.filter(
                        (item) =>
                          item.meet_the_sla === 1 && item.type === "Project"
                      ).length
                    }
                  </td>
                  <td className="text-center">
                    {
                      scoring.filter(
                        (item) =>
                          item.meet_the_sla === 0 && item.type === "Project"
                      ).length
                    }
                  </td>
                </tr>
              ))}
              <tr className="[&>td]:p-2 hover:bg-slate-200 border-b divide-x divide-slate-200 border-slate-200">
                <td colSpan={2} className="font-bold text-center">
                  Total
                </td>
                <td colSpan={2} className="font-bold text-center">
                  {data.gap_scorings.length}
                </td>
                <td className="font-bold text-center">
                  {
                    data.gap_scorings.filter(
                      (item) =>
                        item.status_pekerjaan === "Done" &&
                        item.type === "Assessment"
                    ).length
                  }
                </td>
                <td className="font-bold text-center">
                  {
                    data.gap_scorings.filter(
                      (item) =>
                        item.status_pekerjaan === "On Progress" &&
                        item.type === "Assessment"
                    ).length
                  }
                </td>
                <td className="font-bold text-center">
                  {
                    data.gap_scorings.filter(
                      (item) =>
                        item.status_pekerjaan === "Done" &&
                        item.type === "Project"
                    ).length
                  }
                </td>
                <td className="font-bold text-center">
                  {
                    data.gap_scorings.filter(
                      (item) =>
                        item.status_pekerjaan === "On Progress" &&
                        item.type === "Project"
                    ).length
                  }
                </td>
                <td className="font-bold text-center">
                  {
                    data.gap_scorings.filter(
                      (item) =>
                        item.meet_the_sla === 1 && item.type === "Project"
                    ).length
                  }
                </td>
                <td className="font-bold text-center">
                  {
                    data.gap_scorings.filter(
                      (item) =>
                        item.meet_the_sla === 0 && item.type === "Project"
                    ).length
                  }
                </td>
              </tr>

            </tbody>
          </table>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

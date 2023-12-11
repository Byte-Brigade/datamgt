import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { ConvertDate } from "@/Utils/ConvertDate";
import { Head } from "@inertiajs/react";

export default function Detail({
  auth,
  seesions,
  branch,
  positions,
  licenses,
}) {
  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={`Inquery Data | Branch | ${branch.branch_name}`} />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div className="overflow-x-auto">
            <table className="w-full text-left table-auto min-w-max">
              <thead className="border-b-2 border-slate-200">
                <tr className="[&>th]:p-2 bg-slate-100">
                  <th>Nama Cabang</th>
                  <th>{branch.branch_name}</th>
                  <th>Status Gedung</th>
                  <th>{branch.status}</th>

                </tr>
              </thead>
              <tbody>
                <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                  <td className="font-bold">Opening Date</td>
                  <td>{ConvertDate(branch.open_date)}</td>
                  <td className="font-bold">Jatuh Tempo Sewa / Sertifikat</td>
                  <td>{ConvertDate(branch.expired_date)}</td>

                </tr>


                <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                  <td className="font-bold">Alamat</td>
                  <td className="w-[200px]">{branch.address}</td>
                  <td className="font-bold">Biaya Sewa / Pembelian</td>
                  <td>
                    {branch.total_biaya_sewa
                      ? branch.total_biaya_sewa.toLocaleString("id-ID")
                      : "-"}
                  </td>
                </tr>
                <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                  <td className="font-bold">NPWP</td>
                  <td>{branch.npwp}</td>
                  <td></td>
                  <td></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div className="grid grid-cols-2 gap-2 mt-4">
            <div className="flex flex-col">
              <span className="mb-2">Staff Cabang</span>
              <table className="w-full text-left">
                <thead className="border-b-2 border-slate-200">
                  <tr className="[&>th]:p-2 bg-slate-100">
                    <th>Jabatan</th>
                    <th>Jumlah</th>
                  </tr>
                </thead>
                <tbody>
                  {positions.map((position) => (
                    <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                      <td>{position.position_name}</td>
                      <td>
                        {
                          branch.employees.filter(
                            (employee) => employee.position_id === position.id
                          ).length
                        }
                      </td>
                    </tr>
                  ))}
                </tbody>
              </table>
            </div>
            <div className="flex flex-col">
              <span className="mb-2">Lisensi</span>
              <div className="overflow-x-auto">
                <table className="w-full text-left">
                  <thead className="border-b-2 border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="w-[50%]">Jenis</th>
                      <th>Remark</th>
                      <th>Jatuh Tempo</th>
                      <th>Lampiran</th>
                    </tr>
                  </thead>
                  <tbody>
                    {licenses.map((license) => (
                      <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                        <td>{license.name}</td>
                        <td>{license.remark}</td>
                        <td>{ConvertDate(license.jatuh_tempo)}</td>
                        {license.url && (
                          <td><a
                            className="text-blue-500 hover:underline text-ellipsis"
                            href={`/storage/${license.url}`}
                            target="__blank"
                          >
                            Lihat
                          </a></td>
                        )}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

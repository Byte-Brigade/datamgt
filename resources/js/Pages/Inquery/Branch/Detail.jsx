import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import { useFormContext } from "@/Components/Context/FormProvider";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { ConvertDate } from "@/Utils/ConvertDate";
import { PhotoIcon } from "@heroicons/react/24/outline";
import { Head } from "@inertiajs/react";

export default function Detail({
  auth,
  seesions,
  branch,
  positions,
  licenses,
  kdos,
}) {
  console.log(branch);
  console.log(positions);
  console.log(kdos);
  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={`Inquery Data | Branch | ${branch.branch_name}`} />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded gap-y-4">
          <div className="grid grid-cols-2">
            <div className="overflow-x-auto">
              <table className="w-full text-left table-auto min-w-max">
                <thead className="border-b-2 border-slate-200">
                  <tr className="[&>th]:p-2 bg-slate-100">
                    <th>Nama Cabang</th>
                    <th>{branch.branch_name}</th>
                  </tr>
                </thead>
                <tbody>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">Opening Date</td>
                    <td>{ConvertDate(branch.open_date)}</td>
                  </tr>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">Alamat</td>
                    <td className="w-[200px]">{branch.address}</td>
                  </tr>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">NPWP</td>
                    <td>{branch.npwp}</td>
                  </tr>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">Status Gedung</td>
                    <td>{branch.status}</td>
                  </tr>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">Jatuh Tempo Sewa / Sertifikat</td>
                    <td>{ConvertDate(branch.expired_date)}</td>
                  </tr>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">Pemilik Gedung</td>
                    <td>{branch.owner}</td>
                  </tr>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">No Telp Pemilik</td>
                    <td>-</td>
                  </tr>
                  <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                    <td className="font-bold">Biaya Sewa / Pembelian</td>
                    <td>
                      {branch.total_biaya_sewa
                        ? branch.total_biaya_sewa.toLocaleString("id-ID")
                        : "-"}
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
            <div className="px-4">
              <div className="flex items-center justify-center w-full overflow-hidden border max-h-[500px] border-slate-200 rounded-xl bg-slate-100">
                {branch.photo ? (
                  <img
                    src={`/storage/ops/branches/${branch.slug}/${branch.photo}`}
                    alt={`Photo ${branch.branch_name}`}
                    className="object-cover w-full h-full"
                    loading="lazy"
                  />
                ) : (
                  <PhotoIcon className="h-auto w-fit text-slate-200" />
                )}
              </div>
            </div>
          </div>

          <div className="grid grid-cols-2 gap-x-2 gap-y-4">
            <div className="flex flex-col gap-y-2">
              <span className="text-lg font-semibold">Staff Cabang</span>
              <div className="relative overflow-y-auto max-h-96">
                <table className="w-full overflow-auto text-left">
                  <thead className="sticky top-0 border-b-2 border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th>Jabatan</th>
                      <th>Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                    <StaffRow positions={positions} branch={branch} />
                  </tbody>
                  <tfoot>
                    <tr className="[&>td]:p-2 hover:bg-slate-200 border-b bg-slate-100 border-slate-200">
                      <td className="font-semibold">Total</td>
                      <td className="font-semibold">
                        {branch.employees.length}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div className="flex flex-col mb-2 gap-y-2">
              <span className="text-lg font-semibold">Staff Support</span>
              <div className="relative overflow-y-auto max-h-96">
                <table className="w-full overflow-auto text-left">
                  <thead className="sticky top-0 border-b-2 border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th>Jabatan</th>
                      <th>Jumlah</th>
                    </tr>
                  </thead>
                  <tbody>
                    <StaffRow
                      positions={positions}
                      branch={branch}
                      type={"alih_daya"}
                    />
                  </tbody>
                  <tfoot>
                    <tr className="[&>td]:p-2 hover:bg-slate-200 border-b bg-slate-100 border-slate-200">
                      <td className="font-semibold">Total</td>
                      <td className="font-semibold">
                        {branch.gap_alih_dayas.length}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
            <div className="flex flex-col col-span-2 mb-2 gap-y-2">
              <span className="text-lg font-semibold">Lisensi</span>
              <div className="overflow-x-auto">
                <table className="w-full text-left">
                  <thead className="border-b-2 border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th className="w-[35%]">Jenis</th>
                      <th>Status</th>
                      <th>No. Izin</th>
                      <th className="w-[15%]">Tanggal Izin</th>
                      <th className="w-[15%]">Tanggal Jatuh Tempo</th>
                      <th>Lampiran</th>
                    </tr>
                  </thead>
                  <tbody>
                    {licenses.map((license) => (
                      <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
                        <td>{license.name}</td>
                        <td>{license.remark}</td>
                        <td>
                          {license.name === "Izin OJK" ? branch.izin : "-"}
                        </td>
                        <td>-</td>
                        <td>{ConvertDate(license.jatuh_tempo)}</td>
                        {license.url && (
                          <td>
                            <a
                              className="text-blue-500 hover:underline text-ellipsis"
                              href={`/storage/${license.url}`}
                              target="__blank"
                            >
                              Lihat
                            </a>
                          </td>
                        )}
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            </div>
            <div className="flex flex-col col-span-2 gap-y-2">
              <span className="text-lg font-semibold">KDO</span>
              <div className="overflow-x-auto">
                <table className="w-full text-left">
                  <thead className="border-b-2 border-slate-200">
                    <tr className="[&>th]:p-2 bg-slate-100">
                      <th>No</th>
                      <th>Vendor</th>
                      <th>Nopol</th>
                      <th>Awal Sewa</th>
                      <th>Akhir Sewa</th>
                      <th>Biaya Sewa</th>
                    </tr>
                  </thead>
                  <tbody>
                    {kdos
                      .filter((kdo) => kdo.biaya_sewa > 0)
                      .map((kdo, index) => (
                        <tr
                          key={kdo.id}
                          className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200"
                        >
                          <td>{index + 1}</td>
                          <td>{kdo.vendor}</td>
                          <td>{kdo.nopol}</td>
                          <td>{ConvertDate(kdo.awal_sewa)}</td>
                          <td>{ConvertDate(kdo.akhir_sewa)}</td>
                          <td className="tabular-nums">
                            {kdo.biaya_sewa.toLocaleString("id-ID")}
                          </td>
                        </tr>
                      ))}
                  </tbody>
                  <tfoot>
                    <tr
                      className={`[&>td]:p-2 bg-slate-100 hover:bg-slate-200 border-b border-slate-200`}
                    >
                      <td colSpan={5} className="font-semibold text-center">
                        Total
                      </td>
                      <td className="font-semibold">
                        {kdos
                          .reduce((prev, kdo) => prev + kdo.biaya_sewa, 0)
                          .toLocaleString("id-ID")}
                      </td>
                    </tr>
                  </tfoot>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

const StaffRow = ({ positions, branch, type }) => {
  const { groupBy } = useFormContext();
  return type === "alih_daya"
    ? Object.entries(groupBy(branch.gap_alih_dayas, "jenis_pekerjaan")).map(
        ([key, alih_daya]) => (
          <tr className="[&>td]:p-2 hover:bg-slate-200 border-b border-slate-200">
            <td>{key}</td>
            <td>{alih_daya.length}</td>
          </tr>
        )
      )
    : positions
        .sort((a, b) => {
          let pa = a.position_name.toLowerCase();
          let pb = b.position_name.toLowerCase();

          return (pa > pb ? 1 : -1) || 0;
        })
        .map(
          (position) =>
            branch.employees.filter(
              (employee) => employee.position_id === position.id
            ).length > 0 && (
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
            )
        );
};

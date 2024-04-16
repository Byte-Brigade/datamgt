export default function NoData({length}) {
  return (
    <tr>
      <td
        colSpan={length}
        className="p-2 text-lg font-semibold text-center bg-slate-200"
      >
        Tidak ada data tersedia
      </td>
    </tr>
  );
}

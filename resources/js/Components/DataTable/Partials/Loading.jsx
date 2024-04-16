export default function Loading({ length }) {
  return (
    <tr>
      <td
        colSpan={length}
        className="p-2 text-lg font-semibold text-center transition-colors duration-75 bg-slate-200 animate-pulse"
      >
        Loading ...
      </td>
    </tr>
  );
}

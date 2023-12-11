export default function TableRow({
  key, data, isSelected, className, isShiftPressed, onClick, children
}) {
  const selectedClass = isSelected ? 'bg-slate-200 ' : '';

  return (
    <tr key={key} className={`${className} ${selectedClass} cursor-pointer ${isShiftPressed ? 'select-none' : ''}`} onClick={onClick}>
      {children}
    </tr>
  )
}

export default function TableRow({
  index, data, isSelected, className, isShiftPressed, onClick, children
}) {
  const selectedClass = isSelected ? 'bg-slate-200 ' : '';

  return (
    <tr key={index} className={`${className} ${selectedClass} cursor-pointer ${isShiftPressed ? 'select-none' : ''}`} onClick={onClick}>
      {children}
    </tr>
  )
}

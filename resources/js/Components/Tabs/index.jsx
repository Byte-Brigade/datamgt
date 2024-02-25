export default function Tabs({ data, active, onClick }) {

  return (
    <div className="grid grid-cols-4 p-2 bg-gray-100 gap-y-2 auto-cols-max">
      {data.map((item) => (
        <div
          className="p-2 border border-blue-500 rounded-t-lg cursor-pointer bg-slate-100"
          id={item.value}
        >
          <p>{item.label}</p>
        </div>
      ))}
    </div>
  );
}

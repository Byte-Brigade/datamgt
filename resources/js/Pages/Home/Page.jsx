import { useForm } from "@inertiajs/react";
import Table from "./Table";

export default function Home({ sessions, users }) {
  const { data, setData, post, processing, errors } = useForm({
    file: null,
  });
  const submit = (e) => {
    e.preventDefault();

    post(route("import"));
  };
  console.log(users)
  return (
    <div className="flex flex-col items-center justify-center min-h-screen">
      <h1>Hello World</h1>
      <form onSubmit={submit} encType="multipart/form-data">
        <div className="flex flex-col">
          <label htmlFor="import">Import Excel</label>
          <input
            onChange={(e) => setData("file", e.target.files[0])}
            type="file"
            name="import"
            id="import"
            accept=".xlsx"
          />
          <button
            className="px-4 py-2 text-white bg-green-400 border border-green-600 rounded-xl"
            disabled={processing}
          >
            Import!
          </button>
        </div>
        {sessions.status && <p className="text-green-400 font-semibold">{sessions.message}</p>}
      </form>

      <div className="w-1/2">
        <Table users={users} />
      </div>
    </div>
  );
}

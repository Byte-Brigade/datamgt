import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

export default function Dashboard(props) {
  return (
    <AuthenticatedLayout
      auth={props.auth}
      errors={props.errors}
      header={
        <h2 className="text-xl font-semibold leading-tight text-gray-800">
          Dashboard
        </h2>
      }
    >
      <Head title="Dashboard" />

      <div className="p-4 border-2 border-gray-200 border-dashed rounded-lg dark:border-gray-700">
        <div className="grid grid-cols-2 gap-4 mb-4">
          <div className="flex flex-col bg-gray-50 h-28 dark:bg-gray-800">
            <div className="flex justify-between">
              <h3>Portofolio value</h3>
              <div className="flex items-start">
                <div>Export</div>
                <select name="date" id="date">
                  <option value="month">Last 30 days</option>
                </select>
              </div>
            </div>
            <div>awd</div>
          </div>
          <div className="flex flex-col items-center justify-center rounded bg-gray-50 h-28 dark:bg-gray-800">
            <span>Available Revenue</span>
            <div></div>
            <div>
              <span>30% Available</span>
              <span>37% Unvailable</span>
            </div>
          </div>
        </div>
        <div className="flex flex-col h-48 mb-4 bg-gray-50 dark:bg-gray-800">
          <div className="flex items-center">
            <span>test</span>
            <div>
              <select name="transaction" id="transaction">
                <option value="mai">awdaw</option>
              </select>
              <select name="transaction" id="transaction">
                <option value="mai">awdaw</option>
              </select>
            </div>
          </div>
          <table className="table-auto">
            <thead>
              <tr>
                <th>Song</th>
                <th>Artist</th>
                <th>Year</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>The Sliding Mr. Bones (Next Stop, Pottersville)</td>
                <td>Malcolm Lockyer</td>
                <td>1961</td>
              </tr>
              <tr>
                <td>Witchy Woman</td>
                <td>The Eagles</td>
                <td>1972</td>
              </tr>
              <tr>
                <td>Shining Star</td>
                <td>Earth, Wind, and Fire</td>
                <td>1975</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>

      <div className="py-12">
        <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 gap-2 mb-2">
            <div className="bg-white ">
              <div className="flex justify-between">
                <h3>Portofolio value</h3>
                <div className="flex items-center">
                  <div>Export</div>
                  <select name="date" id="date">
                    <option value="month">Last 30 days</option>
                  </select>
                </div>
              </div>
              <div>test</div>
            </div>
          </div>

          <div className="bg-white">
            <div className="flex justify-between">
              <span>test</span>
              <div>
                <select name="transaction" id="transaction">
                  <option value="mai">awdaw</option>
                </select>
                <select name="transaction" id="transaction">
                  <option value="mai">awdaw</option>
                </select>
              </div>
            </div>
            <table className="table-auto">
              <thead>
                <tr>
                  <th>Song</th>
                  <th>Artist</th>
                  <th>Year</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>The Sliding Mr. Bones (Next Stop, Pottersville)</td>
                  <td>Malcolm Lockyer</td>
                  <td>1961</td>
                </tr>
                <tr>
                  <td>Witchy Woman</td>
                  <td>The Eagles</td>
                  <td>1972</td>
                </tr>
                <tr>
                  <td>Shining Star</td>
                  <td>Earth, Wind, and Fire</td>
                  <td>1975</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

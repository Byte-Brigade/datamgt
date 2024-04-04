import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";

export default function Branch({ sessions, auth }) {
  console.log(auth.user);
  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="Inquery Data | Branch" />
      <div className="p-4 border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col rounded">
          <span className="capitalize">
            Welcome, {auth.user.name}
          </span>
        </div>
      </div>
    </AuthenticatedLayout>
  );
}

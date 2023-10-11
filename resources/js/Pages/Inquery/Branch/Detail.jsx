import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head } from "@inertiajs/react";
import React from "react";

export default function Detail({ auth, seesions, branch }) {
  console.log(branch);
  return (
    <AuthenticatedLayout auth={auth}>
      <Head title={`Inquery Data | Branch | ${branch.branch_name}`} />
      <h2>this is inquery branch detail page.</h2>
      <p>{branch.branch_name}</p>
    </AuthenticatedLayout>
  );
}

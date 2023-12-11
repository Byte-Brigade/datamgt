import Alert from "@/Components/Alert";
import { BreadcrumbsDefault } from "@/Components/Breadcrumbs";
import DataTable from "@/Components/DataTable";
import DropdownMenu from "@/Components/DropdownMenu";
import PrimaryButton from "@/Components/PrimaryButton";
import Modal from "@/Components/Reports/Modal";
import SecondaryButton from "@/Components/SecondaryButton";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { ArrowUpTrayIcon, DocumentPlusIcon } from "@heroicons/react/24/outline";
import { PlusIcon, XMarkIcon } from "@heroicons/react/24/solid";
import { Head, useForm } from "@inertiajs/react";
import { useFormContext } from "@/Components/Context/FormProvider";

import {
  Button,
  Dialog,
  DialogBody,
  DialogFooter,
  DialogHeader,
  IconButton,
  Input,
  Option,
  Select,
  Typography,
} from "@material-tailwind/react";
import { useEffect, useState } from "react";
import { FormProvider } from "@/Components/Context/FormProvider";
export default function Detail({ auth, branch, sessions }) {




  const headings = [
    {
      name: 'Scoring Schedule',
      rowsSpan: 3,
      colsSpan: 2,
    },
    {
      name: 'Jumlah Vendor',
      rowsSpan: 3,
      colsSpan: 2,
    },
    {
      name: 'Type Scoring',
      colsSpan: 7,
    }
  ]
  const columns = [

    {
      name: "Asset Number",
      field: "asset_number",
      className: "text-center",
      sortable: true,
    },
    {
      name: "Asset Description",
      field: "asset_description",
    },
    {
      name: "Date In Place Service",
      field: "date_in_place_service",
      type: "date",
      sortable: true,
    },
    {
      name: "Assst Cost",
      field: "asset_cost",
      className: "text-right",
      type: 'custom',
      sortable: true,
      render: (data) => {
        return data.asset_cost ? data.asset_cost.toLocaleString('id-ID') : 0
      }
    },
    {
      name: "Depre Exp",
      field: "depre_exp",
      className: "text-right",
      sortable: true,
      type: 'custom',
      render: (data) => {
        return data.depre_exp ? data.depre_exp.toLocaleString('id-ID') : 0
      }
    },
    {
      name: "Accum Depre",
      field: "accum_depre",
      className: "text-right",
      type: 'custom',
      sortable: true,
      render: (data) => {
        return data.accum_depre ? data.accum_depre.toLocaleString('id-ID') : 0
      }
    },
    {
      name: "Net Book Value",
      field: "net_book_value",
      className: "text-right",
      sortable: true,
    },
    {
      name: "Asset Location",
      field: "asset_location",
      className: "text-center",
    },
    {
      name: "Major Category",
      field: "major_category",
      className: "text-center",
    },
    {
      name: "Minor Category",
      field: "minor_category",
      className: "text-center",
    },


    {
      name: "Category",
      field: "category",
      className: "text-center",
      sortable: true,
    },

    {
      name: "Remark",
      field: 'remark',
      remark: true,
      method: 'post'
    }
  ];



  return (
    <AuthenticatedLayout auth={auth}>
      <Head title="GA Procurement | Assets" />
      <BreadcrumbsDefault />
      <div className="p-4 border-2 border-gray-200 rounded-lg bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
        <div className="flex flex-col mb-4 rounded">
          <div>{sessions.status && <Alert sessions={sessions} />}</div>

            <DataTable
              columns={columns}
              fetchUrl={`/api/gap/assets`}
              bordered={true}
              submitUrl={`inquery.assets.remark`}
              parameters={{ branch_code: branch.branch_code }}
            >

              <Button type="submit"
                className="inline-flex mr-2 bg-green-500 hover:bg-green-400 active:bg-green-700 focus:bg-green-400"

              >
                Submit
              </Button>
            </DataTable>


        </div>
      </div>
    </AuthenticatedLayout>
  );
}

import { usePage } from "@inertiajs/react";
import { Breadcrumbs } from "@material-tailwind/react";

export function BreadcrumbsDefault() {
  const page = usePage();
  const baseUrl = route().t.url;
  console.log(page);

  const currentRoute = route().current();
  const routeParts = currentRoute.split(".");
  // const routeParts = page.url.split("/").filter((route) => route !== "");
  console.log(routeParts);
  const crumbs = [];
  let url = `${baseUrl}/`;

  if (routeParts.length > 0) {
    routeParts.forEach((crumb, index) => {
      if (index === routeParts.length - 1 && route().params.id) {
        url += `${route().params.id}`;
      } else {
        url += `${crumb}/`;
      }

      crumbs.push({
        name: crumb,
        url: url,
      });
    });
  } else {
    crumbs.push({
      name: "Dashboard",
      url: "/",
    });
  }

  const TitleCrumb = (text) => {
    switch (text) {
      case "branches":
        return "Data Cabang";
      case "employees":
        return "Karyawan Cabang";
      case "gap":
        return "GA Procurement";
      case "infra":
        return "GA Infra";
      case "skbirtgs":
        return "SK BI RTGS";
      case "apar":
        return "APAR";
      case "sk-operasional":
        return "SK Operasional Cabang";
      case "assets":
        return "Asset";
      case "kdos":
        return "KDO Mobil";
      case "toners":
        return "Toner";
      case "alihdayas":
        return "Alih Daya";
      case "pks":
        return "Perjanjian Kerjasama";
      case "perdins":
        return "Biaya Perjalanan Dinas";
      case "stos":
        return "Hasil STO";
      case "disnaker":
        return "Izin Disnaker";
      case "reporting":
        return "Report";
      case "inquery":
        return "Inquery Data";
      case "mobil":
        return "Detail";
      case "scoring-projects":
        return "Scoring Projects";
      case "scoring-assessments":
        return "Scoring Assessments";
      case "sewa_gedungs":
        return "Sewa Gedung";
      case "bros":
        return "Branch Roll Out";
      case "licenses":
        return "Lisensi";
      case "maintenance-costs":
        return "Maintenance and Project Cost";
      case "Histories":
        return "Histories";
      default:
        break;
    }

    if (text.includes("-")) {
      return text
        .split("-")
        .map((t) => t[0].toUpperCase() + t.substring(1))
        .join(" ");
    }

    if (text.includes("%20")) {
      return decodeURI(text);
    }

    return text[0].toUpperCase() + text.substring(1);
  };

  return (
    <Breadcrumbs className="mb-2 bg-gray-100">
      {crumbs.map((crumb, index) =>
        index !== crumbs.length - 1 ? (
          <a key={crumb.name} href={crumb.url} className="opacity-60">
            {TitleCrumb(crumb.name)}
          </a>
        ) : (
          <span key={crumb.name}>{TitleCrumb(crumb.name)}</span>
        )
      )}
    </Breadcrumbs>
  );
}

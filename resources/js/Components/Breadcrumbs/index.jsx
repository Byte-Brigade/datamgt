import { Breadcrumbs } from "@material-tailwind/react";

export function BreadcrumbsDefault() {
  const baseUrl = route().t.url;

  const currentRoute = route().current();
  const routeParts = currentRoute.split(".");
  const crumbs = [];
  let url = `${baseUrl}/`;

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

  const TitleCrumb = (text) => {
    switch (text) {
      case "gap":
        return "GA Procurement";
      case "infra":
        return "GA Infra";
      case "skbirtgs":
        return "SK BI RTGS";
      case "apar":
        return "APAR";
      case "sk-operasional":
        return "SK Operasional";
      case "kdo":
        return "KDO Mobil";
      case "disnaker":
        return "Izin Disnaker";
      case "reporting":
        return "Report";
      case "inquery":
        return "Inquery Data";
      case "mobil":
        return "Detail";
      case "scoring_projects":
        return "Scoring Projects";
      case "scoring_assessments":
        return "Scoring Assessments";
      case "sewa_gedungs":
        return "Sewa Gedung";
      default:
        break;
    }

    if (text.includes("-")) {
      return text
        .split("-")
        .map((t) => t[0].toUpperCase() + t.substring(1))
        .join(" ");
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

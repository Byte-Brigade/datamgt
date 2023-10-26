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

  return (
    <Breadcrumbs className="mb-2">
      {crumbs.map((crumb, index) =>
        index !== crumbs.length - 1 ? (
          <a key={crumb.name} href={crumb.url} className="opacity-60">
            {crumb.name[0].toUpperCase() + crumb.name.substring(1)}
          </a>
        ) : (
          <span key={crumb.name}>
            {crumb.name[0].toUpperCase() + crumb.name.substring(1)}
          </span>
        )
      )}
    </Breadcrumbs>
  );
}

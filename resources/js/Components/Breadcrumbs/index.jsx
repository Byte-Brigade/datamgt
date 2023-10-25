import { Breadcrumbs } from "@material-tailwind/react";

export function BreadcrumbsDefault() {
  const url = route().current();
  let breadcrumbArr = url.split(".");
  let path;
  if (Object.keys(route().params).length > 0) {
    breadcrumbArr.pop();
    path = route(breadcrumbArr.join("."), route().params);
  } else {
    path = route(breadcrumbArr.join("."));
  }
  return (
    <Breadcrumbs className="mb-2">
      {breadcrumbArr.map((bread, index) =>
        index !== breadcrumbArr.length - 1 ? (
          <a key={index} href={path} className="opacity-60">
            {bread[0].toUpperCase() + bread.substring(1)}
          </a>
        ) : (
          <span key={index}>{bread[0].toUpperCase() + bread.substring(1)}</span>
        )
      )}
    </Breadcrumbs>
  );
}

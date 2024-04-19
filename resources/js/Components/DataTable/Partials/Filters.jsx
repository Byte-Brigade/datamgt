import { Button, Checkbox, Collapse } from "@material-tailwind/react";
import React from "react";

export default function Filters({
  open,
  columns,
  filters,
  component,
  filterData,
  handleCheckbox,
  handleCheckboxData,
  handleFilter,
  handleClearFilter,
}) {
  return (
    <div id="filters">
      <Collapse open={open}>
        <div className="w-full mx-auto my-2 bg-slate-200 p-2 rounded-lg shadow-inner">
          <div className="flex flex-col flex-wrap">
            <span className="ml-3 font-medium text-lg">Filters</span>
            {columns
              .filter((column, index) => column.filterable)
              .map((column, id) => {
                if (column.name !== "Action") {
                  return (
                    <div key={id}>
                      <Checkbox
                        label={column.name}
                        key={id}
                        checked={filters.includes(column.field)}
                        value={column.field}
                        onChange={(e) =>
                          handleCheckbox(
                            e.target.value,
                            column.component,
                            column.field
                          )
                        }
                      />
                      {component.length > 0 &&
                        filters.includes(column.field) &&
                        component.map(({ data, field }, i) =>
                          column.field == field ? (
                            <div key={column.field} className="ml-4 grid grid-cols-4">
                              {data.map((item, index) => (
                                <div key={index}>
                                  <Checkbox
                                    onChange={(e) =>
                                      handleCheckboxData(e.target.value, field)
                                    }
                                    checked={
                                      filterData[field]
                                        ? filterData[field].includes(item)
                                        : false
                                    }
                                    key={index}
                                    value={item}
                                    label={item}
                                  />
                                </div>
                              ))}
                            </div>
                          ) : (
                            ""
                          )
                        )}
                    </div>
                  );
                }
              })}
          </div>

          <div className="flex justify-end gap-x-2 mt-2">
            <Button size="sm" onClick={handleClearFilter}>
              Clear
            </Button>
            <Button size="sm" color="green" onClick={handleFilter}>
              Filter
            </Button>
          </div>
        </div>
      </Collapse>
    </div>
  );
}

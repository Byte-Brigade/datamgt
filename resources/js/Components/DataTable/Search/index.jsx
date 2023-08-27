import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import React from "react";

export default function Search({
  perpage,
  handleChangePerpage,
  handleSearch,
  search,
  setSearch,
}) {
  return (
    <div className="flex items-center justify-between mb-4">
      <div className="flex items-center gap-x-2">
        Show
        <select
          name="perpage"
          id="perpage"
          className="rounded-lg bg-slate-100"
          value={perpage.current}
          onChange={handleChangePerpage}
        >
          <option value="10">10</option>
          <option value="20">20</option>
          <option value="50">50</option>
          <option value="100">100</option>
        </select>
        entries
      </div>
      <div>
        <form onSubmit={handleSearch}>
          <div className="flex items-center gap-2">
            <InputLabel htmlFor="search">Search : </InputLabel>
            <TextInput
              type="search"
              name="search"
              value={search}
              onChange={(e) => setSearch(e.target.value)}
            />
            <PrimaryButton type="submit">Cari</PrimaryButton>
          </div>
        </form>
      </div>
    </div>
  );
}

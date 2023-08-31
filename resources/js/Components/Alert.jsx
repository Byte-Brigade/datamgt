import React from "react";

export default function Alert({ sessions }) {
  const status =
    sessions.status[0].toUpperCase() + sessions.status.substring(1);
  return (
    <div
      className={`border-l-4 ${
        sessions.status === "success"
          ? "bg-green-100 border-green-500 text-green-700"
          : "bg-orange-100 border-orange-500 text-orange-700"
      } p-4 mb-4`}
      role="alert"
    >
      <p class="font-bold">{status}</p>
      <p>{sessions.message}</p>
    </div>
  );
}

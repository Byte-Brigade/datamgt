import { toast } from "react-toastify";

export const notify = (status, message) => {
  const id = "notify-id";
  if (status === "success") {
    return toast.success(message);
  }
  if (status === "error") {
    return toast.error(message)
  }
};

import { forwardRef, useEffect, useRef } from "react";

export default forwardRef(function TextInput(
  {
    type = "text",
    className = "",
    isFocused = false,
    size = "default",
    ...props
  },
  ref
) {
  const input = ref ? ref : useRef();

  useEffect(() => {
    if (isFocused) {
      input.current.focus();
    }
  }, []);

  return (
    <div className="flex flex-col items-start">
      <input
        {...props}
        type={type}
        className={
          size === "default"
            ? "form-input border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm "
            : size === "small"
            ? "form-input block w-full p-2 text-gray-900 border border-gray-300 rounded-lg bg-gray-50 sm:text-xs focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500 "
            : "" + className
        }
        ref={input}
      />
    </div>
  );
});

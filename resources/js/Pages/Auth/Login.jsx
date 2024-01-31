import { useEffect } from "react";
import GuestLayout from "@/Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import InputLabel from "@/Components/InputLabel";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Head, Link, useForm } from "@inertiajs/react";

export default function Login({ status, canResetPassword }) {
  const { data, setData, post, processing, errors, reset } = useForm({
    email: "",
    password: "",
  });

  useEffect(() => {
    return () => {
      reset("password");
    };
  }, []);

  const handleOnChange = (event) => {
    setData(
      event.target.name,
      event.target.type === "checkbox"
        ? event.target.checked
        : event.target.value
    );
  };

  const submit = (e) => {
    e.preventDefault();

    post(route("login"));
  };

  return (
    <GuestLayout>
      <Head title="Masuk" />

      <div>
        <h2 className="text-4xl font-semibold text-center">Masuk</h2>
      </div>

      {status && (
        <div className="mb-4 text-sm font-medium text-green-600">{status}</div>
      )}

      <form
        className="p-8 mt-8 border border-black rounded-xl"
        onSubmit={submit}
      >
        <div>
          <InputLabel htmlFor="email" value="User ID" />

          <TextInput
            id="email"
            type="text"
            name="email"
            value={data.email}
            className="block w-full mt-1"
            autoComplete="username"
            isFocused={true}
            onChange={handleOnChange}
          />

          <InputError message={errors.email} className="mt-2" />
        </div>

        <div className="mt-4">
          <InputLabel htmlFor="password" value="Password" />

          <TextInput
            id="password"
            type="password"
            name="password"
            value={data.password}
            className="block w-full mt-1"
            autoComplete="current-password"
            onChange={handleOnChange}
          />

          <InputError message={errors.password} className="mt-2" />
        </div>

        <div className="flex flex-col items-center justify-end mt-4 gap-y-4">
          <PrimaryButton disabled={processing}>Masuk</PrimaryButton>

          {canResetPassword && (
            <Link
              href={route("password.request")}
              className="text-sm text-gray-600 underline rounded-md hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
              Lupa password?
            </Link>
          )}
        </div>
      </form>
    </GuestLayout>
  );
}

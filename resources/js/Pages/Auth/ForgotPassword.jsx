import GuestLayout from "@/Layouts/GuestLayout";
import InputError from "@/Components/InputError";
import PrimaryButton from "@/Components/PrimaryButton";
import TextInput from "@/Components/TextInput";
import { Head, useForm } from "@inertiajs/react";

export default function ForgotPassword({ status }) {
  const { data, setData, post, processing, errors } = useForm({
    email: "",
  });

  const onHandleChange = (event) => {
    setData(event.target.name, event.target.value);
  };

  const submit = (e) => {
    e.preventDefault();

    post(route("password.email"));
  };

  return (
    <GuestLayout>
      <Head title="Forgot Password" />

      <div className="text-sm text-gray-600">
        Reset sementara dilakukan oleh Admin
      </div>

      {/* {status && (
        <div className="mb-4 text-sm font-medium text-green-600">{status}</div>
      )} */}
      {/* <form onSubmit={submit}>
        <TextInput
          id="email"
          type="email"
          name="email"
          value={data.email}
          className="block w-full mt-1"
          isFocused={true}
          onChange={onHandleChange}
        />

        <InputError message={errors.email} className="mt-2" />

        <div className="flex items-center justify-end mt-4">
          <PrimaryButton className="ml-4" disabled={processing}>
            Email Password Reset Link
          </PrimaryButton>
        </div>
      </form> */}
    </GuestLayout>
  );
}

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
      <div className="p-4">
        {status && (
          <div className="mb-4 text-sm font-medium text-green-600">
            {status}
          </div>
        )}
        <form onSubmit={submit}>
          <div className="flex flex-col items-center mt-2 gap-y-2">
            <div className="mb-4 text-sm text-gray-600">
              Forgot your password? No problem. Just let us know your email
              address and we will email you a password reset link that will
              allow you to choose a new one.
            </div>
            <div>
              <InputError message={errors.email} className="mt-2" />
              <TextInput
                id="email"
                type="email"
                name="email"
                value={data.email}
                className="block w-full mt-1"
                isFocused={true}
                onChange={onHandleChange}
                placeholder="Email"
              />
            </div>
            <PrimaryButton disabled={processing}>Reset Password</PrimaryButton>
          </div>
        </form>
      </div>
    </GuestLayout>
  );
}

import { useForm } from '@inertiajs/react';
import React, { createContext, useContext, useState } from 'react';

const FormContext = createContext();


export const FormProvider = ({children, onSubmit, url}) => {
  const [isRefreshed, setIsRefreshed] = useState(false);
  const [initialData, setInitialData] = useState({});
  const form = useForm(initialData);

  const handleFormSubmit = (e) => {
    e.preventDefault();
    form.post(route(url), {
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
      }
    })
  }

  return (
    <FormContext.Provider value={{ handleFormSubmit, isRefreshed, setInitialData, form}}>
      {children}
    </FormContext.Provider>
  )
}

export const useFormContext = () => useContext(FormContext);

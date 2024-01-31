import { useForm } from '@inertiajs/react';
import { createContext, useContext, useState } from 'react';

const FormContext = createContext();


export const FormProvider = ({ children }) => {
  const [isRefreshed, setIsRefreshed] = useState(false);
  const [initialData, setInitialData] = useState({});
  const [url, setUrl] = useState([]);
  const [id, setId] = useState([]);
  const [periode, setPeriode] = useState({
    startDate: null,
    endDate: null
  });
  const [selected, setSelected] = useState({})
  const [modalOpen, setModalOpen] = useState({
    create: false,
    edit: false,
    upload: false,
    import: false,
  });


  const form = useForm(initialData);


 const handleFormEdit = (e) => {
    e.preventDefault();
    form.put(route(url, id), {
      method: "put",
      replace: true,
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setModalOpen(!modalOpen);
      },
    });
  };

  const handleFormSubmit = (e) => {
    e.preventDefault();
    form.post(route(url), {
      onFinish: () => {
        setIsRefreshed(!isRefreshed);
        setModalOpen(!modalOpen);
      }
    })
  }

  return (
    <FormContext.Provider value={{
      handleFormSubmit,
      handleFormEdit,
      isRefreshed,
      setInitialData,
      form,
      setUrl,
      modalOpen,
      setModalOpen,
      setId,
      selected, setSelected,
      periode, setPeriode
    }}>
      {children}
    </FormContext.Provider>
  )
}

export const useFormContext = () => useContext(FormContext);

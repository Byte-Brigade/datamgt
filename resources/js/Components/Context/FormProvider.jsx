import { useForm } from '@inertiajs/react';
import { createContext, useContext, useState } from 'react';

const FormContext = createContext();


export const FormProvider = ({ children }) => {
  const [isRefreshed, setIsRefreshed] = useState(false);
  const [initialData, setInitialData] = useState({});
  const [filterData, setFilterData] = useState({});
  const [url, setUrl] = useState([]);
  const [id, setId] = useState(null);
  const [periode, setPeriode] = useState({
    startDate: null,
    endDate: null
  });
  const [selected, setSelected] = useState({})
  const [input, setInput] = useState({})
  const [modalOpen, setModalOpen] = useState({
    create: false,
    edit: false,
    upload: false,
    import: false,
  });

  const groupBy = (array, key) =>
    array.reduce((result, item) => {
      // Extract the value for the current key
      const keyValue = item[key];

      // If the key doesn't exist in the result object, create it with an empty array
      if (!result[keyValue]) {
        result[keyValue] = [];
      }

      // Push the current item to the array associated with the key
      result[keyValue].push(item);

      return result;
    }, {});


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
    if (id) {
      form.post(route(url, id), {
        onFinish: () => {
          setIsRefreshed(!isRefreshed);
          setModalOpen(!modalOpen);
        }
      })
    } else {
      form.post(route(url), {
        onFinish: () => {
          setIsRefreshed(!isRefreshed);
          setModalOpen(!modalOpen);
        }
      })
    }

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
      periode, setPeriode,
      input, setInput,
      groupBy,
      filterData, setFilterData
    }}>
      {children}
    </FormContext.Provider>
  )
}

export const useFormContext = () => useContext(FormContext);

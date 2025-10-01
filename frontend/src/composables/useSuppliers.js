import axios from "@/axios";
import { ref, reactive, warn } from "vue";

export function getSuppliers() {
  const suppliers = ref([]);
  const isLoading = ref(false);

  const fetchSuppliers = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/suppliers/index", {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      if (response.status === 200) {
        suppliers.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return { suppliers, isLoading, fetchSuppliers };
}

export function manageSupplier() {
  const errors = ref(null);

  const supplierCred = reactive({
    encrypted_id: "",
    supplier_name: "",
    contact_person: "",
    phone: "",
    email: "",
    address: "",
  });

  const addSupplier = async (supplierCred) => {
    try {
      const response = await axios.post("/api/suppliers/store", supplierCred, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
      if (error.response.data.errors) {
        errors.value = error.response.data.errors;
      }
      console.log(errors.value);
    }
  };

  const updateSupplier = async (supplierCred) => {
    try {
      const response = await axios.put("/api/suppliers/update", supplierCred, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
      if (error.response.data.errors) {
        errors.value = error.response.data.errors;
      }
      console.log(errors.value);
    }
  };

  const deleteSupplier = async (id) => {
    try {
      const response = await axios.delete("/api/suppliers/destroy", {
        data: {
          encrypted_id: id,
        },
        headers: {
          "Content-Type": "application/json",
        },
      });

      return response;
    } catch (error) {
      console.log(error);
    }
  };
  return { errors, updateSupplier, supplierCred, deleteSupplier, addSupplier };
}

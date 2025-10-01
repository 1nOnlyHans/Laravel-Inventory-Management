import axios from "@/axios";
import { reactive, ref } from "vue";
export function getCategories() {
  const categories = ref([]);
  const isLoading = ref(false);

  const fetchCategories = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/categories/index", {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      if (response.status === 200) {
        categories.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return { categories, isLoading, fetchCategories };
}

export function manageCategories() {
  const categoryCred = reactive({
    encrypted_id: "",
    category_name: "",
    category_description: "",
  });
  const isLoading = ref(false);
  const errors = ref(null);

  const addCategory = async (categoryCred) => {
    try {
      const response = await axios.post("/api/categories/store", categoryCred, {
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

  const updateCategory = async (categoryCred) => {
    try {
      const response = await axios.put("/api/categories/update", categoryCred, {
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

  const deleteCategory = async (id) => {
    try {
      const response = await axios.delete("/api/categories/destroy", {
        data: {
          encrypted_id: id,
        },
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    }
  };
  return { categoryCred, errors, addCategory, updateCategory, deleteCategory };
}

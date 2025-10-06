import axios from "@/axios";
import { ref } from "vue";
export function getBrands() {
  const brands = ref([]);
  const isLoading = ref(false);

  const fetchBrands = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/brands/index");
      console.log(response);
      if (response.status === 200) {
        brands.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return { brands, isLoading, fetchBrands };
}

export function manageBrands() {
  const errors = ref([]);

  const addBrand = async (brandCred) => {
    try {
      const response = await axios.post("/api/brands/store", brandCred, {
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
    }
  };

  const updateBrand = async (brandCred) => {
    try {
      const response = await axios.put("/api/brands/update", brandCred, {
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
    }
  };

  const deleteBrand = async (id) => {
    try {
      const response = await axios.delete("/api/brands/destroy", {
        data: {
          brand_id: id,
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

  return { errors, addBrand, updateBrand, deleteBrand };
}

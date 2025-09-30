import axios from "@/axios";
import { ref } from "vue";

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

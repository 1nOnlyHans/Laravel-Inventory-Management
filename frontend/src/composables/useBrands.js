import axios from "@/axios";
import { ref } from "vue";
export function getBrands() {
  const brands = ref([]);
  const isLoading = ref(false);

  const fetchBrands = async () => {
    try {
      const response = await axios.get("/api/brands/index");
      console.log(response);
      if (response.status === 200) {
        brands.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { brands, isLoading, fetchBrands };
}

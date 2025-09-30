import axios from "@/axios";
import { ref } from "vue";
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

import axios from "@/axios";
import { ref } from "vue";
export function getProducts() {
  const products = ref([]);
  const isLoading = ref(false);

  const fetchProducts = async () => {
    try {
      const response = await axios.get("/api/products/index", {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      if (response.status === 200) {
        products.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { products, isLoading, fetchProducts };
}

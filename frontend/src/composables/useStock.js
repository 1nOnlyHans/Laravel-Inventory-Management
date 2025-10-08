import axios from "@/axios";
import { ref } from "vue";

export function manageStock() {
  const isLoading = ref(false);

  const stockIn = async (product_ids) => {
    try {
      const response = await axios.post("/api/stocks/stockin", {
        product_id: product_ids,
      });
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  return { isLoading, stockIn };
}

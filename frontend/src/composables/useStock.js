import axios from "@/axios";
import { faL } from "@fortawesome/free-solid-svg-icons";
import { ref } from "vue";

export function getStocksMovements() {
  const movements = ref([]);
  const isLoading = ref(false);

  const fetchMovements = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/stocks/index");
      if (response.status === 200) {
        movements.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return { movements, isLoading, fetchMovements };
}
export function manageStock() {
  const isLoading = ref(false);

  const stockIn = async (product_ids, reference_no) => {
    try {
      const response = await axios.post("/api/stocks/stockin", {
        product_id: product_ids,
        reference_no: reference_no,
      });
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  return { isLoading, stockIn };
}

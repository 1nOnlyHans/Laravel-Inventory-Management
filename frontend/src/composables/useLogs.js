import axios from "@/axios";
import { ref } from "vue";

export function getLogs() {
  const logs = ref([]);
  const isLoading = ref(false);

  const fetchLogs = async () => {
    try {
      const response = await axios.get("/api/logs/index");
      if (response.status === 200) {
        logs.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { logs, isLoading, fetchLogs };
}

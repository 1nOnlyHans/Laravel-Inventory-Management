import axios from "@/axios";
import { ref } from "vue";

export function getAlerts() {
  const alerts = ref([]);
  const isLoading = ref(false);

  const fetchAlerts = async () => {
    try {
      const response = await axios.get("/api/alerts/index");
      if (response.status === 200) {
        alerts.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { alerts, isLoading, fetchAlerts };
}

import axios from "@/axios";
import { ref } from "vue";
export function getConfig() {
  const config = ref({
    config_key: "tax",
    config_value: null,
  });

  const profit = ref({
    config_key: "profit",
    config_value: null,
  });

  const fetchConfig = async () => {
    try {
      const response = await axios.get("/api/system/index");
      if (response.status === 200) {
        config.value.config_value = response.data[0].config_value;
        profit.value.config_value = response.data[1].config_value;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { config, profit, fetchConfig };
}
export function manageTax() {
  const setTax = async (cred) => {
    try {
      const response = await axios.post("/api/system/config", cred);
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  return { setTax };
}

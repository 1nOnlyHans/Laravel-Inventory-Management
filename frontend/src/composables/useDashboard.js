import axios from "@/axios";
import { ref } from "vue";

export function getDashboardDatas() {
  const datas = ref([]);

  const fetchDashboardDatas = async () => {
    try {
      const response = await axios.get("/api/admin/dashboard", {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      if (response.status === 200) {
        datas.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { datas, fetchDashboardDatas };
}

export function POSdashboard() {
  const datas = ref([]);

  const fetchDashboardDatas = async () => {
    try {
      const response = await axios.get("/api/staff/dashboard");
      console.log(response);
      if (response.status === 200) {
        datas.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { datas, fetchDashboardDatas };
}

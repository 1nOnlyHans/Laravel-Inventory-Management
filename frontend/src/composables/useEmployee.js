import axios from "@/axios";
import Swal from "sweetalert2";

import { ref } from "vue";
export function getEmployees() {
  const employees = ref([]);
  const isLoading = ref(false);
  const pagination = ref(null);

  // Fetching of Employees
  const fetchEmployees = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/employees/index");
      console.log(response);
      if (response.status === 200) {
        // Employees Container
        employees.value = response.data.employees.data;
        //Pagination Value
        pagination.value = response.data.employees;
      }
    } catch (error) {
      console.log(error);
      Swal.fire({
        icon: "error",
        title: "Unexpected Error",
        text: "Check console for errors",
      });
    } finally {
      isLoading.value = false;
    }
  };

  return { employees, isLoading, fetchEmployees, pagination };
}

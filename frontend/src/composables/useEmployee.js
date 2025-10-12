import axios from "@/axios";
import Swal from "sweetalert2";

import { reactive, ref } from "vue";
export function getEmployees() {
  const employees = ref([]);
  const isLoading = ref(false);
  const pagination = ref(null);
  const employeeDetails = ref(null);
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

  const fetchEmployeesData = async (id) => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/employees/show/" + id, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      if (response.status === 200) {
        employeeDetails.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return {
    employees,
    isLoading,
    fetchEmployees,
    pagination,
    fetchEmployeesData,
    employeeDetails,
  };
}

export function manageEmployee() {
  const employeCred = reactive({
    firstname: "",
    middlename: "",
    lastname: "",
    email: "",
    gender: "",
    dob: "",
  });

  const errors = ref(null);
  const resetEmployeeCred = () => {
    Object.assign(employeCred, {
      firstname: "",
      middlename: "",
      lastname: "",
      email: "",
      gender: "",
      dob: "",
      role: "",
    });
    errors.value = null;
  };
  const addEmployee = async () => {
    try {
      const response = await axios.post("/api/employees/store", employeCred, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      if (response.status === 200) {
        resetEmployeeCred();
      }
      return response;
    } catch (error) {
      console.log(error);
      if (error.response.data.errors) {
        errors.value = error.response.data.errors;
      }
    }
  };

  const updateEmployee = async (employeeCred) => {
    try {
      const response = await axios.put("/api/employees/update", employeeCred, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      return response;
    } catch (error) {
      console.log(error);
      if (error.response.data.errors) {
        errors.value = error.response.data.errors;
      }
      console.log(errors.value);
    }
  };

  const updateEmployeeAccount = async (employeeCred) => {
    try {
      const response = await axios.put(
        "/api/employees/updateAccount",
        employeeCred,
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      );
      return response;
    } catch (error) {
      console.log(error);
      if (error.response.data.errors) {
        errors.value = error.response.data.errors;
      }
      console.log(errors.value);
    }
  };

  const deleteEmployee = async (id) => {
    try {
      const response = await axios.delete("/api/employee/destroy", {
        data: {
          employee_id: id,
        },
      });
      return response;
    } catch (error) {
      console.log(error);
      return error;
    }
  };
  return {
    employeCred,
    addEmployee,
    errors,
    resetEmployeeCred,
    updateEmployee,
    updateEmployeeAccount,
    deleteEmployee,
  };
}

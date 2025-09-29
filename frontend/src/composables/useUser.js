import axios from "@/axios";
import { reactive } from "vue";

export function manageUsers() {
  const userCred = reactive({
    employee_id: "",
    role: "",
  });

  const createUser = async () => {
    try {
      const response = await axios.post("/api/users/store", userCred, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  return { userCred, createUser };
}

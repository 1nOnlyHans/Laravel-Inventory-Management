import axios from "@/axios";
import { ref } from "vue";

export function CSVImport() {
  const loadingImport = ref(false);
  const importStaff = async (file) => {
    loadingImport.value = true;
    if (!file) {
      return alert("Invalid File");
    }
    const formData = new FormData();
    formData.append("file", file);
    try {
      const response = await axios.post("/api/csv/staff", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      return response;
    } catch (error) {
      console.log(error);
      return error;
    } finally {
      loadingImport.value = false;
    }
  };

  return { loadingImport, importStaff };
}

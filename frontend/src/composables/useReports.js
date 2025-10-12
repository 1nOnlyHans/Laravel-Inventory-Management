import axios from "@/axios";

export function generateReports() {
  const generateInventoryReports = async () => {
    try {
      const response = await axios.get("/api/pdf/inventory", {
        responseType: "blob",
      });
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateMovementReport = async () => {
    try {
      const response = await axios.get("/api/pdf/movement", {
        responseType: "blob",
      });
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generatePurchaseHistoryReport = async () => {
    try {
      const response = await axios.get("/api/pdf/purchase", {
        responseType: "blob",
      });
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateSalesReport = async () => {
    try {
      const response = await axios.get("/api/pdf/sales", {
        responseType: "blob",
      });
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateLowStockReport = async () => {
    try {
      const response = await axios.get("/api/pdf/lowStock", {
        responseType: "blob",
      });
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateOutOfstockReport = async () => {
    try {
      const response = await axios.get("/api/pdf/outOfStock", {
        responseType: "blob",
      });
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };
  return {
    generateInventoryReports,
    generateMovementReport,
    generatePurchaseHistoryReport,
    generateSalesReport,
    generateLowStockReport,
    generateOutOfstockReport,
  };
}

import axios from "@/axios";

export function generateReports() {
  const generateInventoryReports = async (filter) => {
    try {
      const response = await axios.post(
        "/api/pdf/inventory",
        {
          filter: filter,
        },
        {
          responseType: "blob",
        }
      );
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateMovementReport = async (filter) => {
    try {
      const response = await axios.post(
        "/api/pdf/movement",
        {
          filter: filter,
        },
        {
          responseType: "blob",
        }
      );
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generatePurchaseHistoryReport = async (filter) => {
    try {
      const response = await axios.post(
        "/api/pdf/purchase",
        {
          filter: filter,
        },
        {
          responseType: "blob",
        }
      );
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateSalesReport = async (filter) => {
    try {
      const response = await axios.post(
        "/api/pdf/sales",
        {
          filter: filter,
        },
        {
          responseType: "blob",
        }
      );
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateLowStockReport = async (filter) => {
    try {
      const response = await axios.post(
        "/api/pdf/lowStock",
        {
          filter: filter,
        },
        {
          responseType: "blob",
        }
      );
      const file = new Blob([response.data], { type: "application/pdf" });
      const fileUrl = window.URL.createObjectURL(file);
      window.open(fileUrl);
    } catch (error) {
      console.log(error);
    }
  };

  const generateOutOfstockReport = async (filter) => {
    try {
      const response = await axios.post(
        "/api/pdf/outOfStock",
        {
          filter: filter,
        },
        {
          responseType: "blob",
        }
      );
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

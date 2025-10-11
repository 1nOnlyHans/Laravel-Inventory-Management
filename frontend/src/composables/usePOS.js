import axios from "@/axios";
import { ref } from "vue";
export function managePOS() {
  const cart = ref(JSON.parse(localStorage.getItem("cart")) || []);

  const addItem = (item) => {
    if (item.product_quantity <= 0) {
      return alert("Out of Stock");
    }
    const exists = cart.value.find((p) => p.product_id === item.encrypted_id);
    console.log(exists);
    if (!exists) {
      cart.value.push({
        product_id: item.encrypted_id,
        product_name: item.product_name,
        brand_name: item.brand.brand_name,
        max_quantity: item.product_quantity,
        quantity: 1,
        unit_price: item.unit_price,
        total_price: 0,
      });
      localStorage.setItem("cart", JSON.stringify(cart.value));
    }
  };

  const removeItem = (index) => {
    cart.value.splice(index, 1);
    localStorage.setItem("cart", JSON.stringify(cart.value));
  };

  const clearItem = () => {
    cart.value = [];
    localStorage.removeItem("cart");
  };

  const sale = async (saleCred) => {
    try {
      const response = await axios.post("/api/staff/sale", saleCred, {
        headers: {
          "Content-Type": "application/json",
        },
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
      return error;
    }
  };
  return { cart, addItem, removeItem, clearItem, sale };
}

export function getTransactions() {
  const transaction = ref([]);
  const isLoading = ref(false);

  const fetchTransactions = async () => {
    try {
      const response = await axios.get("/api/staff/getTransactions");
      console.log(response);
      if (response.status === 200) {
        transaction.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { transaction, isLoading, fetchTransactions };
}

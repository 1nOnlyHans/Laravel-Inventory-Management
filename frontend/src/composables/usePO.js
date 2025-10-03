import axios from "@/axios";
import { faL } from "@fortawesome/free-solid-svg-icons";
import { reactive, ref } from "vue";

export function sendPO() {
  const isLoading = ref(false);
  const addPurchaseOrder = async (purchaseOrder) => {
    isLoading.value = true;
    try {
      const response = await axios.post(
        "/api/purchase_order/store",
        purchaseOrder
      );
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return { addPurchaseOrder, isLoading };
}

export function managePO() {
  const orderItems = ref([]);
  const purchaseCred = reactive({
    supplier_id: "",
    order_date: null,
    expected_date: null,
    items: orderItems,
  });

  const itemCred = reactive({
    purchase_order_id: "",
    product_name: "",
    product_id: "",
    unit_price: 0,
    quantity: 0,
  });

  return { orderItems, itemCred, purchaseCred };
}

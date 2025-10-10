import axios from "@/axios";
import { faL } from "@fortawesome/free-solid-svg-icons";
import { reactive, ref, warn } from "vue";

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

  const markAsDelivered = async (purchase_id) => {
    try {
      const response = await axios.put("/api/purchase/delivered", {
        purchase_id: purchase_id,
      });
      return response;
    } catch (error) {
      console.log(error);
    }
  };
  return { orderItems, itemCred, purchaseCred, markAsDelivered };
}

export function getPurchases() {
  const purchases = ref([]);
  const isLoading = ref(false);

  const fetchPurchases = async () => {
    isLoading.value = true;
    try {
      const response = await axios.get("/api/purchase_order/index");
      console.log(response);
      if (response.status === 200) {
        purchases.value = response.data;
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
    }
  };

  return { purchases, isLoading, fetchPurchases };
}

export function onlinePayment() {
  const paymentCred = reactive({
    order_id: "",
    name: "LapTopia",
    email: "LapTopia@gmail.com",
    contact: "09123456789",
    amount: 1200,
    items: null,
  });

  const isLoading = ref(false);

  const createOnlinePayment = async (paymentCred) => {
    try {
      const response = await axios.post("/api/paymongo/payment", paymentCred);
      console.log(response);

      const store = await storeSession(response.data.data.id);

      if (store.status !== 200) {
        console.log("Error");
        return;
      } else {
        console.log(store);
      }

      if (response.data.data.attributes.checkout_url) {
        window.location.href = response.data.data.attributes.checkout_url;
      }
    } catch (error) {
      console.log(error);
    }
  };

  const storeSession = async (session) => {
    try {
      const response = await axios.post("/api/paymongo/storeSession", {
        purchase_id: paymentCred.order_id,
        session_id: session,
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  const fetchLatestSession = async () => {
    try {
      const response = await axios.get("/api/paymongo/latest");
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    }
  };
  const checkIfSuccessTransaction = async (session, purchase_id) => {
    isLoading.value = true;
    try {
      const response = await axios.post(
        "/api/paymongo/checktransaction",
        {
          session_id: session,
        },
        {
          headers: {
            "Content-Type": "application/json",
          },
        }
      );
      console.log(response);
      if (response.status === 200) {
        if (response.data.data.attributes.paid_at) {
          //Update
          const payment_method =
            response.data.data.attributes.payment_method_used;
          const amount_paid =
            response.data.data.attributes.payments[0].attributes.amount / 100;
          const total_amount =
            response.data.data.attributes.payment_intent.attributes.amount /
            100;
          const reference_no = null;
          await updatePaymentStatus(purchase_id);
          await createPaymentRecord(
            purchase_id,
            payment_method,
            reference_no,
            amount_paid,
            total_amount
          );
        }
      }
    } catch (error) {
      console.log(error);
    } finally {
      isLoading.value = false;
      window.location.href = "/admin/purchase_history";
    }
  };

  const createPaymentRecord = async (
    purchase_id,
    payment_method,
    reference_no = null,
    amount_paid,
    total_amount,
    order_id = ""
  ) => {
    try {
      const response = await axios.post("/api/purchase/record", {
        purchase_id: purchase_id,
        payment_method: payment_method,
        reference_no: reference_no,
        amount_paid: amount_paid,
        total_amount: total_amount,
        order_id: order_id,
      });
      console.log(response);
      return response;
    } catch (error) {
      console.log(error);
    }
  };

  const updatePaymentStatus = async (purchase_id) => {
    try {
      const response = await axios.put("/api/purchase/updatestatus", {
        purchase_id: purchase_id,
      });
      console.log(response);
    } catch (error) {
      console.log(error);
    }
  };

  return {
    paymentCred,
    createOnlinePayment,
    checkIfSuccessTransaction,
    fetchLatestSession,
    isLoading,
    createPaymentRecord,
    updatePaymentStatus,
  };
}

export function getPurchaseData() {
  const purchaseData = ref(false);

  const fetchPurchaseData = async (id) => {
    try {
      const response = await axios.get("/api/purchase/show/" + id);
      console.log(response);
      if (response.status === 200) {
        purchaseData.value = response.data;
      }
    } catch (error) {
      console.log(error);
    }
  };

  return { purchaseData, fetchPurchaseData };
}

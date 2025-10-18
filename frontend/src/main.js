import { createApp } from "vue";
import App from "@/App.vue";
import router from "@/router";
import "@/assets/main.css";
import GuestLayout from "@/views/Layouts/GuestLayout.vue";
import AuthLayout from "@/views/Layouts/AuthLayout.vue";
import { VueSpinnersPlugin } from "vue3-spinners";
import "vue3-toastify/dist/index.css";
import { toast } from "vue3-toastify";
import { createPinia } from "pinia";
import Echo from "laravel-echo";
import Pusher from "pusher-js";
const pinia = createPinia();
var app = createApp(App);

window.Pusher = Pusher;

window.Echo = new Echo({
  broadcaster: "pusher",
  key: "be8e402f2e51d0b4f291",
  cluster: "ap1",
  forceTLS: true,
});

window.Echo.channel("stock").listen(".stock.stockin", (event) => {
  toast.success(`Stock In: ${event.product.product_name}`, {
    position: "bottom-right",
    autoClose: 3000,
    theme: "colored",
  });
});

window.Echo.channel("sold").listen(".sold.created", (event) => {
  const transaction = event.transaction;
  toast.success(
    `New Sale! Customer: ${transaction.customer_name}, Total: ₱${transaction.total_amount}`,
    {
      position: "bottom-right",
      autoClose: 3000,
      theme: "colored",
    }
  );
});

window.Echo.channel("stock").listen(".stock.lowstock", (event) => {
  toast.warning(`Low Stock: ${event.product.product_name}`, {
    position: "bottom-right",
    autoClose: 3000,
    theme: "colored",
  });
});

window.Echo.channel("stock").listen(".stock.nostock", (event) => {
  toast.error(`Out of Stock: ${event.product.product_name}`, {
    position: "bottom-right",
    autoClose: 3000,
    theme: "colored",
  });
});

window.Echo.channel("purchase").listen(".purchase.accept", (event) => {
  toast.success(
    `Your Order Has been Accepted By: ${event.purchase.supplier_name}`,
    {
      position: "bottom-right",
      autoClose: 3000,
      theme: "colored",
    }
  );
});

window.Echo.channel("purchase").listen(".purchase.reject", (event) => {
  toast.error(
    `Your Order Has been Rejected By: ${event.purchase.supplier_name}`,
    {
      position: "bottom-right",
      autoClose: 3000,
      theme: "colored",
    }
  );
});

app.component("GuestLayout", GuestLayout);
app.component("AuthLayout", AuthLayout);
app.use(VueSpinnersPlugin);
app.use(router);
app.use(pinia);
app.mount("#app");

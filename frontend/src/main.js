import { createApp } from "vue";
import App from "@/App.vue";
import router from "@/router";
import "@/assets/main.css";
import GuestLayout from "@/views/Layouts/GuestLayout.vue";
import AuthLayout from "@/views/Layouts/AuthLayout.vue";
import { VueSpinnersPlugin } from "vue3-spinners";
import { createPinia } from "pinia";

const pinia = createPinia();
var app = createApp(App);
app.component("GuestLayout", GuestLayout);
app.component("AuthLayout", AuthLayout);
app.use(VueSpinnersPlugin);
app.use(router);
app.use(pinia);
app.mount("#app");

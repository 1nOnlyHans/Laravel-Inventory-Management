import { defineStore } from "pinia";
import axios from "@/axios";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    isAuthenticated: false,
  }),

  actions: {
    async login(username, password) {
      const csrf = await axios.get("/sanctum/csrf-cookie");
      if (csrf.status === 204) {
        const response = await axios.post("/api/login", {
          username,
          password,
        });

        if (response.status === 200) {
          this.user = response.data.user;
          this.token = response.data.token;
          this.isAuthenticated = true;
          localStorage.setItem("token", this.token);
        }
        return response;
      }
    },

    async fetchUser() {
      try {
        const response = await axios.get("/api/getAuthUser");
        if (response.status === 200) {
          this.isAuthenticated = true;
          this.user = response.data.user;
        }
      } catch (error) {
        console.log(error);
      }
    },

    async logout() {
      const response = await axios.post("/api/logout");

      if (response.status === 200) {
        localStorage.removeItem("token");
        this.$reset();
      }

      return response;
    },
  },
});

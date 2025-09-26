import { defineStore } from "pinia";
import axios from "@/axios";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user")) || null,
        token: localStorage.getItem("token") || null,
        isAuthenticated: !!localStorage.getItem("token"),
    }),

    actions: {
        async login(email, password) {
            const csrf = await axios.get("/sanctum/csrf-cookie");
            if (csrf.status === 204) {
                const response = await axios.post("/api/login", {
                    email,
                    password,
                });

                if (response.status === 200) {
                    this.user = response.data.user;
                    this.token = response.data.token;
                    this.isAuthenticated = true;

                    localStorage.setItem("token", this.token);
                    localStorage.setItem("user", JSON.stringify(this.user));
                }

                return response;
            }
        },

        async logout() {
            const response = await axios.post("/api/logout");

            if (response.status === 200) {
                this.user = null;
                this.token = null;
                this.isAuthenticated = false;

                localStorage.removeItem("token");
                localStorage.removeItem("user");
            }

            return response;
        },
    },
})

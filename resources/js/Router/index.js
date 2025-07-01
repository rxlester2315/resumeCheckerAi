import { Component } from "lucide";
import { createRouter, createWebHashHistory } from "vue-router";
import landingPage from "../components/UI/Landing.vue";
import LoginAuth from "../components/Auth/Login.vue";

const routes = [
    {
        path: "/",
        name: "UI.Landing",
        component: landingPage,
    },

    {
        path: "/login",
        name: "Auth.Login",
        component: LoginAuth,
    },
];
const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-row justify-center items-center gap-12 py-3">
                <div class="text-center">
                    <a
                        :href="route('ai-checker')"
                        class="text-lg font-semibold text-gray-800 hover:text-blue-600 transition duration-300"
                        >Ai Checker</a
                    >
                </div>

                <div class="text-center">
                    <a
                        :href="route('create-resume')"
                        class="text-lg font-semibold text-gray-800 hover:text-blue-600 transition duration-300"
                        >Create Resume</a
                    >
                </div>

                <div class="text-center">
                    <a
                        :href="route('ai-analytics')"
                        class="text-lg font-semibold text-gray-800 hover:text-blue-600 transition duration-300"
                        >Ai Analytics</a
                    >
                </div>
            </div>
        </template>
    </AuthenticatedLayout>
</template>

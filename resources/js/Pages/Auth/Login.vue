<script setup>
import Checkbox from "@/components/Checkbox.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/components/InputError.vue";
import InputLabel from "@/components/InputLabel.vue";
import PrimaryButton from "@/components/PrimaryButton.vue";
import TextInput from "@/components/TextInput.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const submit = () => {
    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="max-w-md w-full mx-auto px-4 py-8">
            <div class="text-center mb-8">
                <h1
                    class="text-3xl font-bold text-gray-900 dark:text-white mb-2"
                >
                    Welcome back
                </h1>
                <p class="text-gray-500 dark:text-gray-400">
                    Sign in to your account to continue
                </p>
            </div>

            <div
                v-if="status"
                class="mb-4 p-4 rounded-lg bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 text-sm"
            >
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-6">
                <div class="space-y-2">
                    <InputLabel
                        for="email"
                        value="Email"
                        class="text-gray-700 dark:text-gray-300"
                    />

                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full py-2.5 px-4 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition duration-200"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                        placeholder="Enter your email"
                    />

                    <InputError
                        class="mt-1 text-sm"
                        :message="form.errors.email"
                    />
                </div>

                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <InputLabel
                            for="password"
                            value="Password"
                            class="text-gray-700 dark:text-gray-300"
                        />
                        <Link
                            v-if="canResetPassword"
                            :href="route('password.request')"
                            class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors"
                        >
                            Forgot password?
                        </Link>
                    </div>

                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full py-2.5 px-4 rounded-lg border border-gray-300 dark:border-gray-700 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-1 focus:ring-indigo-500 dark:focus:ring-indigo-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition duration-200"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                        placeholder="Enter your password"
                    />

                    <InputError
                        class="mt-1 text-sm"
                        :message="form.errors.password"
                    />
                </div>

                <div class="flex items-center">
                    <Checkbox
                        name="remember"
                        v-model:checked="form.remember"
                        class="h-4 w-4 rounded border-gray-300 dark:border-gray-600 text-indigo-600 dark:text-indigo-500 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-800"
                    />
                    <label
                        class="ms-2 block text-sm text-gray-700 dark:text-gray-300"
                    >
                        Remember me
                    </label>
                </div>

                <PrimaryButton
                    class="w-full justify-center py-2.5 px-4 rounded-lg transition duration-200"
                    :class="{ 'opacity-75': form.processing }"
                    :disabled="form.processing"
                >
                    <span v-if="!form.processing">Log in</span>
                    <span v-else class="flex items-center">
                        <svg
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                        >
                            <circle
                                class="opacity-25"
                                cx="12"
                                cy="12"
                                r="10"
                                stroke="currentColor"
                                stroke-width="4"
                            ></circle>
                            <path
                                class="opacity-75"
                                fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                            ></path>
                        </svg>
                        Signing in...
                    </span>
                </PrimaryButton>
            </form>

            <div
                class="mt-6 text-center text-sm text-gray-500 dark:text-gray-400"
            >
                Don't have an account?
                <Link
                    :href="route('register')"
                    class="font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors"
                >
                    Sign up
                </Link>
            </div>
        </div>
    </GuestLayout>
</template>

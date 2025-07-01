<script setup>
import { ref } from "vue";
import axios from "axios";

// Reactive state
const file = ref(null);
const uploading = ref(false);
const error = ref(null);
const errorHint = ref("");
const results = ref(null);
const extractedText = ref("");
const successMessage = ref("");
const dragOver = ref(false);
const uploadProgress = ref(0);

// Format file size for display
const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat(bytes / Math.pow(k, i)).toFixed(2) + " " + sizes[i];
};

// Handle file selection
const handleFileUpload = (event) => {
    if (event.dataTransfer && event.dataTransfer.files) {
        file.value = event.dataTransfer.files[0];
    } else if (event.target.files) {
        file.value = event.target.files[0];
    }
    error.value = null;
    errorHint.value = "";
    results.value = null;
    extractedText.value = "";
};

// Handle drag and drop
const handleDrop = (e) => {
    dragOver.value = false;
    handleFileUpload(e);
};

// Copy text to clipboard
const copyToClipboard = () => {
    navigator.clipboard.writeText(extractedText.value);
    successMessage.value = "Text copied to clipboard!";
    setTimeout(() => (successMessage.value = ""), 3000);
};

// Main upload function

const uploadResume = async () => {
    // Reset states
    error.value = null;
    errorHint.value = "";
    successMessage.value = "";

    if (!file.value) {
        error.value = "Please select a file first";
        return;
    }

    // Client-side validation
    const validTypes = [
        "application/pdf",
        "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
    ];
    if (!validTypes.includes(file.value.type)) {
        error.value = "Invalid file type";
        errorHint.value = "Please upload PDF or DOCX only";
        return;
    }

    if (file.value.size > 2 * 1024 * 1024) {
        error.value = "File too large";
        errorHint.value = "Maximum file size is 2MB";
        return;
    }

    const currentFile = file.value;
    uploading.value = true;
    uploadProgress.value = 0;

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);

    try {
        const formData = new FormData();
        formData.append("resume", currentFile);

        const response = await axios.post("/upload", formData, {
            headers: { "Content-Type": "multipart/form-data" },
            signal: controller.signal,
            onUploadProgress: (progressEvent) => {
                if (progressEvent.total) {
                    uploadProgress.value = Math.round(
                        (progressEvent.loaded * 100) / progressEvent.total
                    );
                }
            },
        });

        clearTimeout(timeout);

        if (!response.data?.success) {
            throw new Error(
                response.data?.message || "Invalid server response"
            );
        }

        results.value = {
            ...response.data,
            uploaded_at: new Date().toLocaleString(),
        };
        extractedText.value = response.data.extracted_text || "";
        successMessage.value = "Upload successful!";
    } catch (err) {
        error.value = err.response?.data?.message || err.message;
        errorHint.value =
            err.code === "ECONNABORTED"
                ? "Upload timed out (30s)"
                : "Please try again";
        console.error("Upload error:", err);
    } finally {
        uploading.value = false;
        uploadProgress.value = 0;
        file.value = null;
        document.getElementById("resume-upload").value = "";
    }
};
</script>

<template>
    <div class="flex flex-row justify-center items-center gap-12 py-3">
        <div class="text-center">
            <a
                :href="route('dashboard')"
                class="text-lg font-semibold text-gray-800 hover:text-blue-600 transition duration-300"
                >Home</a
            >
        </div>
    </div>

    <div
        class="bg-slate-600 min-h-screen w-full flex items-center justify-center p-6"
    >
        <div
            class="bg-white shadow-lg rounded-xl w-full p-4 md:p-8 space-y-4 md:space-y-6 max-w-md mx-4 md:max-w-xl"
        >
            <!-- Heading -->
            <div class="text-center">
                <h1 class="text-2xl font-bold text-gray-800">
                    📁 Resume Storage
                </h1>
                <p class="text-gray-600 mt-2">
                    Securely store and manage your resumes
                </p>
            </div>

            <!-- Success Message -->
            <div
                v-if="successMessage"
                class="bg-green-50 border-l-4 border-green-500 p-4"
            >
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-green-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">
                            {{ successMessage }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="bg-red-50 border-l-4 border-red-500 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg
                            class="h-5 w-5 text-red-500"
                            viewBox="0 0 20 20"
                            fill="currentColor"
                        >
                            <path
                                fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">
                            {{ error }}
                            <span v-if="errorHint" class="block mt-1 text-xs">{{
                                errorHint
                            }}</span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Upload box -->
            <div
                @dragover.prevent="dragOver = true"
                @dragleave="dragOver = false"
                @drop.prevent="handleDrop"
                :class="{ 'border-blue-500 bg-blue-50': dragOver }"
                class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center cursor-pointer transition-colors duration-200"
            >
                <p class="text-gray-500 mb-4">
                    <span v-if="!dragOver"
                        >Drop your resume here or choose a file</span
                    >
                    <span v-else class="text-blue-600"
                        >Drop your file to upload</span
                    >
                </p>
                <input
                    type="file"
                    id="resume-upload"
                    @change="handleFileUpload"
                    accept=".pdf,.docx"
                    class="hidden"
                />
                <label
                    for="resume-upload"
                    class="mx-auto text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer inline-block"
                >
                    Select File
                </label>
                <p v-if="file" class="text-sm text-gray-600 mt-2 file-info">
                    Selected: {{ file.name }} ({{ formatFileSize(file.size) }})
                </p>
                <p class="text-xs text-gray-400 mt-2">
                    PDF & DOCX only · Max 2MB
                </p>
            </div>

            <!-- Progress bar -->
            <div
                v-if="uploadProgress > 0"
                class="w-full bg-gray-200 rounded-full h-2.5"
            >
                <div
                    class="bg-blue-600 h-2.5 rounded-full"
                    :style="{ width: uploadProgress + '%' }"
                ></div>
            </div>

            <!-- Upload button -->
            <div class="text-center">
                <button
                    @click="uploadResume"
                    :disabled="!file || uploading"
                    class="relative bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg transition duration-200 inline-flex items-center justify-center disabled:bg-blue-400 disabled:cursor-not-allowed min-w-32"
                >
                    <!-- Loading State -->
                    <span v-if="uploading" class="absolute flex items-center">
                        <svg
                            class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                            :class="{ 'opacity-50': uploadProgress === 100 }"
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
                        {{
                            uploadProgress > 0
                                ? `Uploading (${uploadProgress}%)`
                                : "Processing..."
                        }}
                    </span>

                    <!-- Normal State -->
                    <span :class="{ 'opacity-0': uploading }"
                        >Upload Resume</span
                    >
                </button>

                <!-- Debug Info (remove in production) -->

                <p class="text-xs text-gray-400 mt-2">
                    🔒 Stored securely. You can manage resumes in "My Resumes".
                </p>
            </div>

            <!-- Results Section -->
            <div v-if="results" class="mt-6 border-t pt-4">
                <div class="flex justify-between items-center mb-2">
                    <h3 class="font-semibold text-lg">Upload Details</h3>
                    <button
                        @click="copyToClipboard"
                        class="text-sm text-blue-600 hover:text-blue-800 flex items-center"
                    >
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="h-4 w-4 mr-1"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"
                            />
                        </svg>
                        Copy Text
                    </button>
                </div>

                <div class="space-y-4">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-1">
                            File Information:
                        </h4>
                        <ul class="text-sm space-y-1">
                            <li>
                                <strong>Name:</strong>
                                {{ results.original_name }}
                            </li>
                            <li>
                                <strong>Type:</strong>
                                {{ results.file_type.toUpperCase() }}
                            </li>
                            <li>
                                <strong>Size:</strong>
                                {{ formatFileSize(results.file_size) }}
                            </li>
                            <li>
                                <strong>Uploaded:</strong>
                                {{ new Date().toLocaleString() }}
                            </li>
                        </ul>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="font-medium text-gray-700 mb-1">
                            Extracted Text Preview:
                        </h4>
                        <pre
                            class="whitespace-pre-wrap text-sm bg-white p-2 rounded border max-h-60 overflow-y-auto"
                        >
              {{ extractedText }}
            </pre
                        >
                        <p class="text-xs text-gray-500 mt-2">
                            Showing first
                            {{ Math.min(extractedText.length, 1000) }}
                            characters
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Custom scrollbar for results */
::-webkit-scrollbar {
    width: 8px;
}
::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}
::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}
::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Mobile optimizations */
@media (max-width: 640px) {
    .file-info {
        word-break: break-all;
    }
}
</style>

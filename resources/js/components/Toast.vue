<template>
    <transition name="toast">
        <div
            v-if="visible"
            :class="[
                'fixed top-4 right-4 z-50 max-w-md w-full shadow-lg rounded-lg p-4',
                type === 'error' ? 'bg-red-50 border border-red-200' : 'bg-green-50 border border-green-200'
            ]"
        >
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg
                        v-if="type === 'error'"
                        class="h-5 w-5 text-red-400"
                        viewBox="0 0 20 20"
                        fill="currentColor"
                    >
                        <path
                            fill-rule="evenodd"
                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                            clip-rule="evenodd"
                        />
                    </svg>
                    <svg
                        v-else
                        class="h-5 w-5 text-green-400"
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
                <div class="ml-3 flex-1">
                    <p
                        :class="[
                            'text-sm font-medium',
                            type === 'error' ? 'text-red-800' : 'text-green-800'
                        ]"
                    >
                        {{ message }}
                    </p>
                    <ul
                        v-if="errors && Object.keys(errors).length > 0"
                        class="mt-2 text-sm text-red-700 list-disc list-inside"
                    >
                        <li v-for="(errorList, field) in errors" :key="field">
                            <span class="font-medium">{{ field }}:</span>
                            <span v-for="(error, index) in errorList" :key="index">
                                {{ error }}
                                <span v-if="index < errorList.length - 1">, </span>
                            </span>
                        </li>
                    </ul>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button
                        @click="$emit('close')"
                        :class="[
                            'inline-flex rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2',
                            type === 'error' ? 'text-red-400 hover:text-red-500 focus:ring-red-500' : 'text-green-400 hover:text-green-500 focus:ring-green-500'
                        ]"
                    >
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                            <path
                                fill-rule="evenodd"
                                d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                clip-rule="evenodd"
                            />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </transition>
</template>

<script setup>
defineProps({
    visible: {
        type: Boolean,
        default: false,
    },
    message: {
        type: String,
        default: '',
    },
    errors: {
        type: Object,
        default: () => ({}),
    },
    type: {
        type: String,
        default: 'error',
        validator: (value) => ['error', 'success'].includes(value),
    },
});

defineEmits(['close']);
</script>

<style scoped>
.toast-enter-active,
.toast-leave-active {
    transition: all 0.3s ease;
}

.toast-enter-from {
    opacity: 0;
    transform: translateX(100%);
}

.toast-leave-to {
    opacity: 0;
    transform: translateX(100%);
}
</style>


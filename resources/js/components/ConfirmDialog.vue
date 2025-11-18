<template>
    <div
        v-if="visible"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="$emit('cancel')"
    >
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ title }}</h3>
            <p class="text-gray-600 mb-6">{{ message }}</p>
            <div class="flex justify-end space-x-3">
                <BaseButton variant="secondary" @click="$emit('cancel')">
                    Cancel
                </BaseButton>
                <BaseButton variant="danger" @click="$emit('confirm')">
                    Confirm
                </BaseButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import BaseButton from './BaseButton.vue';

defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    title: {
        type: String,
        default: 'Confirm Action',
    },
    message: {
        type: String,
        required: true,
    },
});

defineEmits(['confirm', 'cancel']);
</script>


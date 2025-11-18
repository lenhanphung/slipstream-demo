<template>
    <div
        v-if="visible"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <h2 class="text-xl font-semibold mb-4">
                {{ contact ? 'Edit Contact' : 'Create Contact' }}
            </h2>
            
            <form @submit.prevent="handleSubmit">
                <div class="mb-4">
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        id="first_name"
                        v-model="formData.first_name"
                        type="text"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <p v-if="errors.first_name" class="mt-1 text-sm text-red-600">
                        {{ errors.first_name }}
                    </p>
                </div>
                
                <div class="mb-4">
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name
                    </label>
                    <input
                        id="last_name"
                        v-model="formData.last_name"
                        type="text"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <p v-if="errors.last_name" class="mt-1 text-sm text-red-600">
                        {{ errors.last_name }}
                    </p>
                </div>
                
                <div class="flex justify-end space-x-3 mt-6">
                    <BaseButton type="button" variant="secondary" @click="$emit('close')">
                        Back
                    </BaseButton>
                    <BaseButton type="submit" variant="primary" :loading="loading">
                        Save
                    </BaseButton>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import BaseButton from './BaseButton.vue';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    contact: {
        type: Object,
        default: null,
    },
    customerId: {
        type: Number,
        default: null,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['save', 'close']);

const formData = ref({
    first_name: '',
    last_name: '',
    customer_id: props.customerId || null,
});

const errors = ref({});

watch(() => props.visible, (newVal) => {
    if (newVal) {
        if (props.contact) {
            formData.value = {
                first_name: props.contact.first_name || '',
                last_name: props.contact.last_name || '',
                customer_id: props.customerId,
            };
        } else {
            formData.value = {
                first_name: '',
                last_name: '',
                customer_id: props.customerId,
            };
        }
        errors.value = {};
    }
});

watch(() => props.customerId, (newVal) => {
    if (newVal) {
        formData.value.customer_id = newVal;
    }
});

const handleSubmit = () => {
    errors.value = {};
    
    if (!formData.value.first_name.trim()) {
        errors.value.first_name = 'First Name is required';
        return;
    }
    
    // Ensure customer_id is included if available
    const data = {
        ...formData.value,
        customer_id: formData.value.customer_id || props.customerId || null,
    };
    
    emit('save', data);
};
</script>


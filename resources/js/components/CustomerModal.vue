<template>
    <div
        v-if="visible"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-40 overflow-y-auto py-8"
        @click.self="$emit('close')"
    >
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-4xl w-full mx-4 my-8">
            <h2 class="text-xl font-semibold mb-6">
                {{ customer ? 'Edit Customer' : 'Create Customer' }}
            </h2>
            
            <form @submit.prevent="handleSubmit">
                <div v-if="errors._general" class="mb-4 p-3 bg-red-50 border border-red-200 rounded-md">
                    <p class="text-sm text-red-800">{{ errors._general }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">General</h3>
                        
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                                Name
                            </label>
                            <input
                                id="name"
                                v-model="formData.name"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
                        </div>
                        
                        <div>
                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-1">
                                Reference
                            </label>
                            <input
                                id="reference"
                                v-model="formData.reference"
                                type="text"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="errors.reference" class="mt-1 text-sm text-red-600">
                                {{ typeof errors.reference === 'string' ? errors.reference : errors.reference[0] }}
                            </p>
                        </div>
                        
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                                Category
                            </label>
                            <select
                                id="category"
                                v-model="formData.customer_category_id"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            >
                                <option value="">Select Category</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p v-if="errors.customer_category_id" class="mt-1 text-sm text-red-600">
                                {{ errors.customer_category_id }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="space-y-4">
                        <h3 class="text-lg font-medium text-gray-900">Details</h3>
                        
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                                Start Date
                            </label>
                            <input
                                id="start_date"
                                v-model="formData.start_date"
                                type="date"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            />
                            <p v-if="errors.start_date" class="mt-1 text-sm text-red-600">{{ errors.start_date }}</p>
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                Description
                            </label>
                            <textarea
                                id="description"
                                v-model="formData.description"
                                rows="4"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            ></textarea>
                            <p v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</p>
                        </div>
                    </div>
                </div>
                
                <div class="border-t pt-4">
                    <ContactTable
                        :contacts="contacts"
                        :customer-id="props.customer?.id || formData.id || 0"
                        :loading="loadingContacts"
                        @create="handleCreateContact"
                        @edit="handleEditContact"
                        @delete="handleDeleteContact"
                    />
                </div>
                
                <div class="flex justify-end space-x-3 mt-6 border-t pt-4">
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
import ContactTable from './ContactTable.vue';

const props = defineProps({
    visible: {
        type: Boolean,
        required: true,
    },
    customer: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        required: true,
    },
    contacts: {
        type: Array,
        default: () => [],
    },
    loading: {
        type: Boolean,
        default: false,
    },
    loadingContacts: {
        type: Boolean,
        default: false,
    },
    serverErrors: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['save', 'close', 'create-contact', 'edit-contact', 'delete-contact', 'save-and-create-contact']);

const formData = ref({
    name: '',
    reference: '',
    customer_category_id: '',
    start_date: '',
    description: '',
});

const errors = ref({});

watch(() => props.visible, (newVal) => {
    if (newVal) {
        if (props.customer) {
            formData.value = {
                id: props.customer.id,
                name: props.customer.name || '',
                reference: props.customer.reference || '',
                customer_category_id: props.customer.customer_category_id || '',
                start_date: props.customer.start_date || '',
                description: props.customer.description || '',
            };
        } else {
            formData.value = {
                name: '',
                reference: '',
                customer_category_id: '',
                start_date: '',
                description: '',
            };
        }
        errors.value = {};
    }
});

watch(() => props.customer, (newCustomer) => {
    if (newCustomer && newCustomer.id && !formData.value.id) {
        // Update formData when customer is set (e.g., after creating new customer)
        formData.value.id = newCustomer.id;
    }
}, { deep: true });

watch(() => props.serverErrors, (newErrors) => {
    if (newErrors && Object.keys(newErrors).length > 0) {
        // Convert server errors format to display format
        const formattedErrors = {};
        Object.keys(newErrors).forEach(key => {
            const errorValue = newErrors[key];
            if (Array.isArray(errorValue)) {
                formattedErrors[key] = errorValue[0]; // Take first error message
            } else {
                formattedErrors[key] = errorValue;
            }
        });
        errors.value = formattedErrors;
    }
}, { immediate: true, deep: true });

const handleSubmit = () => {
    if (validateForm()) {
        emit('save', { ...formData.value });
    }
};

const handleCreateContact = async () => {
    const customerId = props.customer?.id || formData.value.id;
    if (customerId) {
        emit('create-contact', customerId);
    } else {
        // If creating new customer, validate and save first
        if (validateForm()) {
            // Emit special event to save customer first, then create contact
            emit('save-and-create-contact', { ...formData.value });
        } else {
            errors.value = {
                ...errors.value,
                _general: 'Please fill in all required fields before adding contacts.'
            };
        }
    }
};

const validateForm = () => {
    errors.value = {};
    
    if (!formData.value.name.trim()) {
        errors.value.name = 'Name is required';
        return false;
    }
    
    if (!formData.value.reference.trim()) {
        errors.value.reference = 'Reference is required';
        return false;
    }
    
    if (!formData.value.customer_category_id) {
        errors.value.customer_category_id = 'Category is required';
        return false;
    }
    
    if (!formData.value.start_date) {
        errors.value.start_date = 'Start Date is required';
        return false;
    }
    
    return true;
};

const handleEditContact = (contact) => {
    emit('edit-contact', contact);
};

const handleDeleteContact = (contact) => {
    emit('delete-contact', contact);
};
</script>


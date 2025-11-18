<template>
    <div class="mt-4">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold">Contacts</h3>
            <BaseButton 
                variant="primary" 
                @click="$emit('create')"
            >
                Create
            </BaseButton>
        </div>
        <div class="overflow-x-auto relative min-h-[200px]">
            <div v-if="loading" class="absolute inset-0 flex items-center justify-center">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            </div>
            <table v-else class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            First Name
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Last Name
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr 
                        v-for="contact in contacts" 
                        :key="contact.id"
                        :class="{ 'bg-yellow-50': isPendingContact(contact) }"
                    >
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ contact.first_name }}
                            <span v-if="isPendingContact(contact)" class="ml-2 text-xs text-yellow-600">(Pending)</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ contact.last_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button
                                type="button"
                                @click="$emit('edit', contact)"
                                class="text-blue-600 hover:text-blue-900 mr-3"
                            >
                                Edit
                            </button>
                            <button
                                type="button"
                                @click="handleDelete(contact)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="contacts.length === 0 && !loading">
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                            No contacts found
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import BaseButton from './BaseButton.vue';

const props = defineProps({
    contacts: {
        type: Array,
        required: true,
    },
    customerId: {
        type: Number,
        required: true,
    },
    loading: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['edit', 'delete', 'delete-pending', 'create']);

const isPendingContact = (contact) => {
    return contact.id && String(contact.id).startsWith('pending-');
};

const handleDelete = (contact) => {
    if (isPendingContact(contact)) {
        emit('delete-pending', contact);
    } else {
        emit('delete', contact);
    }
};
</script>


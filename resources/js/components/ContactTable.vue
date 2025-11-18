<template>
    <div class="mt-4">
        <div class="flex justify-between items-center mb-2">
            <h3 class="text-lg font-semibold">Contacts</h3>
            <BaseButton variant="primary" @click="$emit('create')">
                Create
            </BaseButton>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
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
                    <tr v-for="contact in contacts" :key="contact.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ contact.first_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ contact.last_name }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <button
                                @click="$emit('edit', contact)"
                                class="text-blue-600 hover:text-blue-900 mr-3"
                            >
                                Edit
                            </button>
                            <button
                                @click="$emit('delete', contact)"
                                class="text-red-600 hover:text-red-900"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                    <tr v-if="contacts.length === 0">
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
import BaseButton from './BaseButton.vue';

defineProps({
    contacts: {
        type: Array,
        required: true,
    },
    customerId: {
        type: Number,
        required: true,
    },
});

defineEmits(['edit', 'delete', 'create']);
</script>


<template>
    <div class="flex items-center gap-3 mb-4 flex-wrap">
        <div class="flex-1 min-w-[200px]">
            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
            <input
                id="search"
                v-model="localSearch"
                type="text"
                placeholder="Search customers..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            />
        </div>
        <div class="min-w-[200px]">
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">Category</label>
            <select
                id="category"
                v-model="localCategory"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
                <option :value="null">...Select...</option>
                <option v-for="category in categories" :key="category.id" :value="category.id">
                    {{ category.name }}
                </option>
            </select>
        </div>
        <div class="flex items-end gap-2">
            <BaseButton variant="secondary" @click="handleClear">
                Clear
            </BaseButton>
            <BaseButton variant="primary" @click="handleApply">
                Apply
            </BaseButton>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import BaseButton from './BaseButton.vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const emit = defineEmits(['search', 'filter', 'clear']);

const localSearch = ref('');
const localCategory = ref(null);

const handleApply = () => {
    emit('search', localSearch.value);
    emit('filter', localCategory.value);
};

const handleClear = () => {
    localSearch.value = '';
    localCategory.value = null;
    emit('clear');
};
</script>


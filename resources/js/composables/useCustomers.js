import { ref } from 'vue';
import api from '@/services/api';

export function useCustomers() {
    const customers = ref([]);
    const loading = ref(false);
    const error = ref(null);

    const fetchCustomers = async (searchQuery = '', categoryFilter = null) => {
        loading.value = true;
        error.value = null;
        
        try {
            const params = {};
            if (searchQuery) {
                params.search = searchQuery;
            }
            if (categoryFilter) {
                params.category_id = categoryFilter;
            }
            
            const response = await api.getCustomers(params);
            customers.value = response.data.data || response.data;
        } catch (err) {
            error.value = err.message || 'Failed to fetch customers';
            console.error('Error fetching customers:', err);
        } finally {
            loading.value = false;
        }
    };

    const createCustomer = async (data) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await api.createCustomer(data);
            await fetchCustomers();
            return response.data.data || response.data;
        } catch (err) {
            error.value = err.message || 'Failed to create customer';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateCustomer = async (id, data) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await api.updateCustomer(id, data);
            await fetchCustomers();
            return response.data.data || response.data;
        } catch (err) {
            error.value = err.message || 'Failed to update customer';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const deleteCustomer = async (id) => {
        loading.value = true;
        error.value = null;
        
        try {
            await api.deleteCustomer(id);
            await fetchCustomers();
        } catch (err) {
            error.value = err.message || 'Failed to delete customer';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        customers,
        loading,
        error,
        fetchCustomers,
        createCustomer,
        updateCustomer,
        deleteCustomer,
    };
}


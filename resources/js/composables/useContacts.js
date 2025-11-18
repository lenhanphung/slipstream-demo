import { ref } from 'vue';
import api from '@/services/api';

export function useContacts() {
    const contacts = ref([]);
    const loading = ref(false);
    const error = ref(null);

    const fetchContacts = async (customerId) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await api.getContacts(customerId);
            contacts.value = response.data.data || response.data;
        } catch (err) {
            error.value = err.message || 'Failed to fetch contacts';
            console.error('Error fetching contacts:', err);
        } finally {
            loading.value = false;
        }
    };

    const createContact = async (data) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await api.createContact(data);
            await fetchContacts(data.customer_id);
            return response.data.data || response.data;
        } catch (err) {
            error.value = err.message || 'Failed to create contact';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const updateContact = async (id, data) => {
        loading.value = true;
        error.value = null;
        
        try {
            const response = await api.updateContact(id, data);
            await fetchContacts(data.customer_id);
            return response.data.data || response.data;
        } catch (err) {
            error.value = err.message || 'Failed to update contact';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    const deleteContact = async (id, customerId) => {
        loading.value = true;
        error.value = null;
        
        try {
            await api.deleteContact(id);
            await fetchContacts(customerId);
        } catch (err) {
            error.value = err.message || 'Failed to delete contact';
            throw err;
        } finally {
            loading.value = false;
        }
    };

    return {
        contacts,
        loading,
        error,
        fetchContacts,
        createContact,
        updateContact,
        deleteContact,
    };
}


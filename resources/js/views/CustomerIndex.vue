<template>
    <div class="flex">
        <Sidebar />
        <div class="flex-1 ml-64">
            <div class="container mx-auto px-4 py-8">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">Customers</h1>
                    <BaseButton variant="primary" @click="openCustomerModal(null)">
                        Create
                    </BaseButton>
                </div>

        <SearchBar
            :categories="categories"
            @search="handleSearch"
            @filter="handleFilter"
            @clear="handleClear"
        />

        <CustomerTable
            :customers="customers"
            :loading="loading"
            @edit="openCustomerModal"
            @delete="handleDeleteCustomer"
        />

        <CustomerModal
            :visible="customerModalVisible"
            :customer="selectedCustomer"
            :categories="categories"
            :contacts="customerContacts"
            :pending-contacts="pendingContacts"
            :loading="saving"
            :loading-contacts="loadingContacts"
            :server-errors="customerErrors"
            @save="handleSaveCustomer"
            @close="closeCustomerModal"
            @create-contact="openContactModal"
            @edit-contact="openContactModal"
            @delete-contact="handleDeleteContact"
            @delete-pending-contact="handleDeletePendingContact"
        />

        <ContactModal
            :visible="contactModalVisible"
            :contact="selectedContact"
            :customer-id="currentCustomerId"
            :loading="savingContact"
            @save="handleSaveContact"
            @close="closeContactModal"
        />

        <ConfirmDialog
            :visible="confirmDialogVisible"
            :title="confirmDialogTitle"
            :message="confirmDialogMessage"
            @confirm="confirmAction"
            @cancel="cancelAction"
        />

        <Toast
            :visible="toastVisible"
            :message="toastMessage"
            :errors="toastErrors"
            :type="toastType"
            @close="toastVisible = false"
        />
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useCustomers } from '@/composables/useCustomers';
import { useContacts } from '@/composables/useContacts';
import { useModal } from '@/composables/useModal';
import api from '@/services/api';
import BaseButton from '@/components/BaseButton.vue';
import Sidebar from '@/components/Sidebar.vue';
import SearchBar from '@/components/SearchBar.vue';
import CustomerTable from '@/components/CustomerTable.vue';
import CustomerModal from '@/components/CustomerModal.vue';
import ContactModal from '@/components/ContactModal.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';
import Toast from '@/components/Toast.vue';

const {
    customers,
    loading,
    fetchCustomers,
    createCustomer,
    updateCustomer,
    deleteCustomer: removeCustomer,
} = useCustomers();

const {
    contacts: customerContacts,
    fetchContacts,
    createContact,
    updateContact,
    deleteContact: removeContact,
} = useContacts();

const { isOpen: customerModalVisible, openModal: openCustomerModalBase, closeModal: closeCustomerModalBase } = useModal();
const { isOpen: contactModalVisible, openModal: openContactModalBase, closeModal: closeContactModalBase } = useModal();
const { isOpen: confirmDialogVisible, openModal: openConfirmDialog, closeModal: closeConfirmDialog } = useModal();

const categories = ref([]);
const selectedCustomer = ref(null);
const selectedContact = ref(null);
const currentCustomerId = ref(null);
const saving = ref(false);
const savingContact = ref(false);
const loadingContacts = ref(false);
const customerErrors = ref({});
const toastVisible = ref(false);
const toastMessage = ref('');
const toastErrors = ref({});
const toastType = ref('error');
const pendingContacts = ref([]); // Contacts created but customer not saved yet

const searchQuery = ref('');
const categoryFilter = ref(null);

const confirmDialogTitle = ref('');
const confirmDialogMessage = ref('');
const pendingAction = ref(null);

onMounted(async () => {
    await Promise.all([
        fetchCustomers(),
        fetchCategories(),
    ]);
});

const fetchCategories = async () => {
    try {
        const response = await api.getCustomerCategories();
        categories.value = response.data.data || response.data;
    } catch (error) {
        console.error('Error fetching categories:', error);
    }
};

const handleSearch = (query) => {
    searchQuery.value = query;
    fetchCustomers(query, categoryFilter.value);
};

const handleFilter = (categoryId) => {
    categoryFilter.value = categoryId;
    fetchCustomers(searchQuery.value, categoryId);
};

const handleClear = () => {
    searchQuery.value = '';
    categoryFilter.value = null;
    fetchCustomers();
};

const openCustomerModal = async (customer) => {
    selectedCustomer.value = customer;
    customerErrors.value = {}; // Clear errors when opening modal
    pendingContacts.value = []; // Clear pending contacts when opening modal
    if (customer) {
        loadingContacts.value = true;
        try {
            await fetchContacts(customer.id);
            currentCustomerId.value = customer.id;
        } finally {
            loadingContacts.value = false;
        }
    } else {
        customerContacts.value = [];
        currentCustomerId.value = null;
    }
    openCustomerModalBase();
};

const closeCustomerModal = () => {
    closeCustomerModalBase();
    selectedCustomer.value = null;
    customerContacts.value = [];
    currentCustomerId.value = null;
    pendingContacts.value = []; // Clear pending contacts when closing modal
};

const showToast = (message, errors = {}, type = 'error') => {
    toastMessage.value = message;
    toastErrors.value = errors;
    toastType.value = type;
    toastVisible.value = true;
    setTimeout(() => {
        toastVisible.value = false;
    }, 5000);
};

const handleSaveCustomer = async (data) => {
    saving.value = true;
    customerErrors.value = {};
    try {
        if (selectedCustomer.value) {
            await updateCustomer(selectedCustomer.value.id, data);
            showToast('Customer updated successfully', {}, 'success');
            await fetchCustomers(); // Refresh list to update contact count
            closeCustomerModal();
        } else {
            // Creating new customer - save first, then create pending contacts
            const newCustomer = await createCustomer(data);
            currentCustomerId.value = newCustomer.id;
            selectedCustomer.value = newCustomer; // Update selected customer
            
            // Create all pending contacts with the new customer ID
            if (pendingContacts.value.length > 0) {
                try {
                    for (const contactData of pendingContacts.value) {
                        await createContact({
                            ...contactData,
                            customer_id: newCustomer.id,
                        });
                    }
                    showToast(`Customer and ${pendingContacts.value.length} contact(s) created successfully`, {}, 'success');
                    pendingContacts.value = []; // Clear pending contacts
                } catch (error) {
                    console.error('Error creating pending contacts:', error);
                    showToast('Customer created but some contacts failed to create', {}, 'error');
                }
            } else {
                showToast('Customer created successfully', {}, 'success');
            }
            
            await fetchContacts(newCustomer.id);
            await fetchCustomers(); // Refresh list to update contact count
            // Don't close modal - allow user to add more contacts
        }
    } catch (error) {
        console.error('Error saving customer:', error);
        
        // Extract error message and errors
        const errorMessage = error.message || 'An error occurred while saving the customer';
        const errorDetails = error.errors || {};
        
        // Set errors for modal display
        customerErrors.value = errorDetails;
        
        // Show toast
        showToast(errorMessage, errorDetails, 'error');
    } finally {
        saving.value = false;
    }
};

const handleDeleteCustomer = (customer) => {
    confirmDialogTitle.value = 'Delete Customer';
    confirmDialogMessage.value = `Are you sure you want to delete customer "${customer.name}"?`;
    pendingAction.value = async () => {
        try {
            await removeCustomer(customer.id);
            showToast('Customer deleted successfully', {}, 'success');
            await fetchCustomers();
            closeConfirmDialog();
        } catch (error) {
            console.error('Error deleting customer:', error);
            const errorMessage = error.message || 'An error occurred while deleting the customer';
            showToast(errorMessage, {}, 'error');
        }
    };
    openConfirmDialog();
};

const openContactModal = (contactOrCustomerId) => {
    if (typeof contactOrCustomerId === 'number') {
        selectedContact.value = null;
        currentCustomerId.value = contactOrCustomerId;
    } else {
        selectedContact.value = contactOrCustomerId;
        currentCustomerId.value = contactOrCustomerId.customer_id;
    }
    openContactModalBase();
};

const closeContactModal = () => {
    closeContactModalBase();
    selectedContact.value = null;
    // Don't close customer modal, just refresh contacts
    if (selectedCustomer.value) {
        loadingContacts.value = true;
        fetchContacts(selectedCustomer.value.id).finally(() => {
            loadingContacts.value = false;
        });
    }
};

const handleSaveContact = async (data) => {
    savingContact.value = true;
    try {
        // Ensure customer_id is included
        if (!data.customer_id && currentCustomerId.value) {
            data.customer_id = currentCustomerId.value;
        }

        // Check if customer exists (already saved)
        const customerId = selectedCustomer.value?.id || currentCustomerId.value;
        
        if (selectedContact.value && selectedContact.value.id) {
            // Editing existing contact - update via API
            await updateContact(selectedContact.value.id, data);
            showToast('Contact updated successfully', {}, 'success');
            
            // Refresh contacts list
            if (selectedCustomer.value) {
                loadingContacts.value = true;
                try {
                    await fetchContacts(selectedCustomer.value.id);
                    await fetchCustomers(); // Refresh to update contact count
                } finally {
                    loadingContacts.value = false;
                }
            }
        } else if (customerId) {
            // Creating new contact for existing customer - create via API
            await createContact(data);
            showToast('Contact created successfully', {}, 'success');
            
            // Refresh contacts list
            if (selectedCustomer.value) {
                loadingContacts.value = true;
                try {
                    await fetchContacts(selectedCustomer.value.id);
                    await fetchCustomers(); // Refresh to update contact count
                } finally {
                    loadingContacts.value = false;
                }
            }
        } else {
            // Creating new contact for new customer (not saved yet) - add to pending contacts
            // Generate a temporary ID for display
            const tempContact = {
                ...data,
                id: `pending-${Date.now()}-${Math.random()}`,
                customer_id: null, // Will be set when customer is saved
            };
            pendingContacts.value.push(tempContact);
            showToast('Contact added. Save customer to create all contacts.', {}, 'success');
        }

        closeContactModal();
    } catch (error) {
        console.error('Error saving contact:', error);
        const errorMessage = error.message || 'An error occurred while saving the contact';
        const errorDetails = error.errors || {};
        showToast(errorMessage, errorDetails, 'error');
        throw error;
    } finally {
        savingContact.value = false;
    }
};

const handleDeletePendingContact = (contact) => {
    const index = pendingContacts.value.findIndex(c => c.id === contact.id);
    if (index > -1) {
        pendingContacts.value.splice(index, 1);
    }
};

const handleDeleteContact = (contact) => {
    confirmDialogTitle.value = 'Delete Contact';
    confirmDialogMessage.value = `Are you sure you want to delete contact "${contact.first_name} ${contact.last_name}"?`;
    pendingAction.value = async () => {
        try {
            await removeContact(contact.id, contact.customer_id);
            showToast('Contact deleted successfully', {}, 'success');
            closeConfirmDialog();
            if (selectedCustomer.value) {
                await fetchContacts(selectedCustomer.value.id);
                await fetchCustomers(); // Refresh to update contact count
            }
        } catch (error) {
            console.error('Error deleting contact:', error);
            const errorMessage = error.message || 'An error occurred while deleting the contact';
            showToast(errorMessage, {}, 'error');
        }
    };
    openConfirmDialog();
};

const confirmAction = () => {
    if (pendingAction.value) {
        pendingAction.value();
        pendingAction.value = null;
    }
};

const cancelAction = () => {
    closeConfirmDialog();
    pendingAction.value = null;
};
</script>


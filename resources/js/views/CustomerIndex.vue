<template>
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
            :loading="saving"
            @save="handleSaveCustomer"
            @close="closeCustomerModal"
            @create-contact="openContactModal"
            @edit-contact="openContactModal"
            @delete-contact="handleDeleteContact"
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
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useCustomers } from '@/composables/useCustomers';
import { useContacts } from '@/composables/useContacts';
import { useModal } from '@/composables/useModal';
import api from '@/services/api';
import BaseButton from '@/components/BaseButton.vue';
import SearchBar from '@/components/SearchBar.vue';
import CustomerTable from '@/components/CustomerTable.vue';
import CustomerModal from '@/components/CustomerModal.vue';
import ContactModal from '@/components/ContactModal.vue';
import ConfirmDialog from '@/components/ConfirmDialog.vue';

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

const openCustomerModal = (customer) => {
    selectedCustomer.value = customer;
    if (customer) {
        fetchContacts(customer.id);
        currentCustomerId.value = customer.id;
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
};

const handleSaveCustomer = async (data) => {
    saving.value = true;
    try {
        if (selectedCustomer.value) {
            await updateCustomer(selectedCustomer.value.id, data);
        } else {
            const newCustomer = await createCustomer(data);
            currentCustomerId.value = newCustomer.id;
            await fetchContacts(newCustomer.id);
        }
        closeCustomerModal();
    } catch (error) {
        console.error('Error saving customer:', error);
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
            closeConfirmDialog();
        } catch (error) {
            console.error('Error deleting customer:', error);
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
    if (selectedCustomer.value) {
        fetchContacts(selectedCustomer.value.id);
    }
};

const handleSaveContact = async (data) => {
    savingContact.value = true;
    try {
        if (selectedContact.value) {
            await updateContact(selectedContact.value.id, data);
        } else {
            await createContact(data);
        }
        closeContactModal();
        if (selectedCustomer.value) {
            await fetchContacts(selectedCustomer.value.id);
        }
    } catch (error) {
        console.error('Error saving contact:', error);
    } finally {
        savingContact.value = false;
    }
};

const handleDeleteContact = (contact) => {
    confirmDialogTitle.value = 'Delete Contact';
    confirmDialogMessage.value = `Are you sure you want to delete contact "${contact.first_name} ${contact.last_name}"?`;
    pendingAction.value = async () => {
        try {
            await removeContact(contact.id, contact.customer_id);
            closeConfirmDialog();
            if (selectedCustomer.value) {
                await fetchContacts(selectedCustomer.value.id);
            }
        } catch (error) {
            console.error('Error deleting contact:', error);
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


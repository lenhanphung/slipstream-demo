import axios from 'axios';

const api = axios.create({
    baseURL: '/api',
    headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
    },
});

api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            return Promise.reject(error.response.data);
        }
        return Promise.reject(error);
    }
);

export default {
    getCustomers(params = {}) {
        return api.get('/customers', { params });
    },
    
    getCustomer(id) {
        return api.get(`/customers/${id}`);
    },
    
    createCustomer(data) {
        return api.post('/customers', data);
    },
    
    updateCustomer(id, data) {
        return api.put(`/customers/${id}`, data);
    },
    
    deleteCustomer(id) {
        return api.delete(`/customers/${id}`);
    },
    
    getContacts(customerId) {
        return api.get(`/customers/${customerId}/contacts`);
    },
    
    createContact(data) {
        return api.post('/contacts', data);
    },
    
    updateContact(id, data) {
        return api.put(`/contacts/${id}`, data);
    },
    
    deleteContact(id) {
        return api.delete(`/contacts/${id}`);
    },
    
    getCustomerCategories() {
        return api.get('/customer-categories');
    },
};


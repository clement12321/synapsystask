import axios from 'axios';

export async function getCsrfCookie() {
    return axios.get('/sanctum/csrf-cookie');
}

export async function registerUser(formData) {
    await getCsrfCookie(); // Ensure CSRF token is set
    return axios.post('/register', formData);
}

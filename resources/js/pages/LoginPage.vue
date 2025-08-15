<template>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="../assets/images/synapsys_logo.png" alt="Company Logo" class="company_logo" />
            </div>
            <h2>Login</h2>
            <form @submit.prevent="login">
                <p v-if="error" class="error-msg">{{ error }}</p>
                <div class="form-group">
                    <input type="text" id="username" v-model="form.username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" v-model="form.password" placeholder="Password" required>
                </div>
                <button type="submit" class="auth-button">Login</button>
            </form>
            <p class="auth-link">Don't have an account? <router-link to="/register">Register</router-link></p>
        </div>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            form: {
                username: '',
                password: '',
            },
            error: null,
        };
    },
    methods: {
        async login() {
            this.error = null;

            try {
                await axios.get('/sanctum/csrf-cookie');

                await axios.post('/login', this.form);

                localStorage.setItem('authToken', 'true');
                this.$router.push('/content');

            } catch (err) {
                if (err.response && err.response.status === 401) {
                    this.error = 'Invalid username or password.';
                } else {
                    this.error = err.response?.data?.message || 'Login failed. Please try again.';
                }
            }
        },
    },
};
</script>

<style scoped>
@import '../../css/auth.css';

.error-msg {
    color: #ff4343;
    margin: 0.5rem;
}
</style>

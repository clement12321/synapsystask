<template>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="../assets/images/synapsys_logo.png" alt="Company Logo" class="company_logo" />
            </div>
            <h2>Registration</h2>
            <form @submit.prevent="register">
                <div class="form-group">
                    <input type="text" id="username" v-model="form.username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" id="password" v-model="form.password" placeholder="Password" required>
                    <p v-if="passwordLengthError" class="password-length-error">{{ passwordLengthError }}</p>
                </div>
                <div class="form-group">
                    <input type="password" id="password_confirmation" v-model="form.password_confirmation" placeholder="Confirm Password" required>
                    <p v-if="passwordMatchError" class="password-match-error">{{ passwordMatchError }}</p>

                </div>
                <div class="form-group">
                    <input type="text" id="name" v-model="form.name" placeholder="Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="phone" v-model="form.phone" placeholder="Phone (Optional)">
                </div>
                <div class="form-group">
                    <select id="country" v-model="form.country" class="form-control">
                        <option value="" disabled>Select Country</option>
                        <option v-for="country in countries" :key="country.code" :value="country.name">
                            {{ country.name }}
                        </option>
                    </select>
                </div>
                <button type="submit" class="auth-button">Register</button>
            </form>
            <p class="auth-link">Already have an account? <router-link to="/login">Login</router-link></p>
        </div>
    </div>
</template>

<script>
import countries from '@/data/countries';
import { registerUser } from '@/services/authService';

export default {
    data() {
        return {
            form: {
                username: '',
                password: '',
                password_confirmation: '',
                name: '',
                phone: '',
                country: '',
            },
            countries,
            passwordLengthError: null,
            passwordMatchError: null,
        };
    },
    methods: {
        async register() {
            this.passwordLengthError = null;
            this.passwordMatchError = null;

            if (this.form.password.length < 6) {
                this.passwordLengthError = "Password must be at least 6 characters long";
                return;
            }
            if (this.form.password !== this.form.password_confirmation) {
                this.passwordMatchError = "Password and Confirm Password do not match";
                return;
            }
            try {
                await registerUser(this.form);
                localStorage.setItem('authToken', 'true');
                this.$router.push('/content');
            } catch (error) {
                this.error = error.response?.data?.message || 'Registration failed';
            }
        },
    },
};
</script>

<style scoped>
@import '../../css/auth.css';

.password-length-error, .password-match-error {
    margin: 0.5rem;
    color: #ff4343;
}
</style>

<template>
    <nav class="navbar" :style="{ backgroundColor: corporateColor }">
        <div class="navbar-brand">
            <img :src="logoUrl" alt="Synapsys Logo" class="logo">
        </div>
        <div class="navbar-menu">
            <span class="user-name" v-if="userName">Hello, {{ userName }}</span>
            <router-link to="/content" class="nav-link">Content</router-link>
            <button @click="logout" class="nav-link">Logout</button>
        </div>
    </nav>
</template>

<script>
import logoImg from '@/assets/images/synapsys_logo.png';

export default {
    data() {
        return {
            logoUrl: logoImg,
            userName: null
        };
    },
    created() {
        this.fetchUserName();
    },
    methods: {
        async fetchUserName() {
            try {
                const response = await axios.get('/api/user');
                this.userName = response.data.details?.name || response.data.username;
            } catch (error) {
                console.error('Failed to fetch user name:', error);
            }
        },
        logout() {
            axios.post('/logout')
                .then(() => {
                    localStorage.removeItem('authToken');
                    this.$router.push('/login');
                })
                .catch(error => {
                    console.error('Logout failed:', error);
                });
        }
    }
};
</script>

<style scoped>
.user-name {
    font-size: 1rem;
    font-weight: 500;
    color: #002739;
}

.navbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 2rem;
    color: white;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.logo {
    height: 40px;
    width: auto;
}

.company-name {
    font-size: 1.5rem;
    font-weight: bold;
}

.navbar-menu {
    display: flex;
    align-items: center; /* Ensures all items align vertically */
    gap: 1.5rem;
}

.nav-link {
    color: #002739;
    text-decoration: none;
    font-size: 1rem;
    cursor: pointer;
    background: none;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    transition: background-color 0.3s;
    line-height: 1;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>

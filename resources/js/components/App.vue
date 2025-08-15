<template>
    <router-view></router-view>
</template>

<script>
export default {
    name: 'App',
    created() {
        // Set up axios interceptors or other global configurations here
        this.setupAxiosInterceptors();
    },
    methods: {
        setupAxiosInterceptors() {
            // Add a request interceptor
            axios.interceptors.request.use(
                (config) => {
                    // Add authorization token if available
                    const token = localStorage.getItem('authToken');
                    if (token) {
                        config.headers.Authorization = `Bearer ${token}`;
                    }
                    return config;
                },
                (error) => {
                    return Promise.reject(error);
                }
            );

            // Add a response interceptor
            axios.interceptors.response.use(
                (response) => response,
                (error) => {
                    if (error.response && error.response.status === 401) {
                        // Handle unauthorized access
                        localStorage.removeItem('authToken');
                        this.$router.push('/login');
                    }
                    return Promise.reject(error);
                }
            );
        }
    }
};
</script>

<style>
/* Add any global styles here */
#app {
    font-family: Avenir, Helvetica, Arial, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    color: #2c3e50;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}
</style>

<template>
    <div class="content-page">
        <nav-bar />
        <div class="container">
            <!-- Carousel for featured content -->
            <div class="carousel" v-if="featuredContents.length">
                <h2>Top 10 Featured Content</h2>
                <div class="carousel-container">
                    <div class="carousel-item" v-for="content in featuredContents" :key="content.id">
                        <div class="carousel-card" @click="openModal(content)">
                            <img v-if="content.image_path" :src="'/storage/' + content.image_path" alt="Content image">
                            <h3>{{ content.title }}</h3>
                            <p>{{ truncateText(content.body, 100) }}</p>
                            <div class="author-info">
                                <span v-if="content.user.details">
                                    {{ content.user.details.name }} ({{ content.user.details.country }})
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="content-actions">
                <!-- Country Filter -->
                <div class="filter-section">
                    <label>Filter by Author's Country:</label>
                    <select v-model="selectedCountry" @change="loadContents">
                        <option value="">All Countries</option>
                        <option v-for="country in availableCountries" :value="country" :key="country">
                            {{ country }}
                        </option>
                    </select>
                    <label class="featured-checkbox"> Show Featured Only <input type="checkbox" v-model="showFeaturedOnly" @change="loadContents"></label>
                </div>
                <button @click="showAddModal = true" class="btn-add">Add New Content</button>
            </div>

            <div class="content-list">
                <table class="editable-table">
                    <thead class="table-header">
                    <tr>
                        <th>Title</th>
                        <th>Author (Country)</th>
                        <th>Featured</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr v-for="content in contents" :key="content.id">
                        <td>
                            <div v-if="editingContentId === content.id">
                                <input v-model="editedTitle" ref="editInput" @blur="saveTitle(content)" @keyup.enter="saveTitle(content)" class="inline-edit-input"/>
                            </div>
                            <div v-else @click="startEdit(content)" class="inline-edit-text">
                                {{ content.title }}
                            </div>
                        </td>
                        <td>
                            <span v-if="content.user.details">
                                {{ content.user.details.name }} ({{ content.user.details.country }})
                            </span>
                        </td>
                        <td>
                            <div class="checkbox-wrapper">
                                <input type="checkbox" v-model="content.is_featured" @change="toggleFeatured(content)">
                            </div>
                        </td>
                        <td>
                            <button @click="openModal(content)" class="btn-view">View</button>
                            <button v-if="content.user_id === currentUserId" @click="deleteContent(content.id)" class="btn-delete">Delete</button>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>

            <!-- Modal for adding new content -->
            <div v-if="showAddModal" class="modal">
                <div class="modal-content">
                    <span class="close" @click="showAddModal = false">&times;</span>
                    <h2>Add New Content</h2>
                    <form @submit.prevent="addContent">
                        <div class="form-group">
                            <label>Title</label>
                            <input v-model="newContent.title" type="text" required>
                        </div>
                        <div class="form-group">
                            <label>Content</label>
                            <textarea v-model="newContent.body" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" @change="handleImageUpload">
                        </div>
                        <div class="form-group checkbox-group">
                            <label>
                                <input type="checkbox" v-model="newContent.is_featured"> Featured
                            </label>
                        </div>
                        <button type="submit" class="btn-submit">Submit</button>
                    </form>
                </div>
            </div>

            <!-- Modal for viewing content -->
            <div v-if="selectedContent" class="modal">
                <div class="modal-content">
                    <span class="close" @click="selectedContent = null">&times;</span>
                    <h2>{{ selectedContent.title }}</h2>
                    <img v-if="selectedContent.image_path" :src="'/storage/' + selectedContent.image_path" alt="Content image" class="modal-image">
                    <p>{{ selectedContent.body }}</p>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            contents: [],
            featuredContents: [],
            selectedContent: null,
            showAddModal: false,
            selectedCountry: '',
            showFeaturedOnly: false,
            availableCountries: [],
            newContent: {
                title: '',
                body: '',
                image: null,
                is_featured: false,
            },
            currentUserId: null,
            editingContentId: null,
            editedTitle: '',
        };
    },
    async created() {
        try {
            const response = await axios.get('/api/user');
            this.currentUserId = response.data.id;
        } catch (error) {
            console.error('Error fetching current user:', error);
        }
        await this.loadContents();
        await this.loadFeaturedContents();
        await this.loadAvailableCountries();
    },
    methods: {
        startEdit(content) {
            this.editingContentId = content.id;
            this.editedTitle = content.title;

            this.$nextTick(() => {
                const input = this.$refs.editInput;
                if (input && input.focus) input.focus();
            });
        },
        async saveTitle(content) {
            if (!this.editedTitle.trim()) {
                alert('Title cannot be empty');
                return;
            }
            try {
                const response = await axios.put(`/api/contents/${content.id}`, {
                    title: this.editedTitle
                });
                content.title = response.data.title;
                this.updateFeaturedContent(content);
                this.editingContentId = null;
                this.editedTitle = '';
            } catch (error) {
                console.error('Error updating title:', error);
            }
        },
        updateFeaturedContent(updatedContent) {
            const index = this.featuredContents.findIndex(c => c.id === updatedContent.id);
            if (index !== -1) {
                this.featuredContents[index].title = updatedContent.title;
            }
        },
        async loadContents() {
            try {
                const params = {};
                if (this.selectedCountry) params.country = this.selectedCountry;
                if (this.showFeaturedOnly) params.featured = true;

                const response = await axios.get('/api/contents', { params });
                this.contents = response.data;
            } catch (error) {
                console.error('Error loading contents:', error);
            }
        },
        loadFeaturedContents() {
            axios.get('/api/featured-contents')
                .then(response => {
                    this.featuredContents = response.data;
                })
                .catch(error => {
                    console.error('Error loading featured contents:', error);
                });
        },
        async loadAvailableCountries() {
            try {
                const response = await axios.get('/api/user-details/countries');
                this.availableCountries = response.data;
            } catch (error) {
                console.error('Error loading countries:', error);
            }
        },
        async toggleFeatured(content) {
            const originalValue = content.is_featured;
            try {
                const response = await axios.put(`/api/contents/${content.id}`, {
                    is_featured: content.is_featured
                });
                content.is_featured = response.data.is_featured;
                await this.loadFeaturedContents();
            } catch (error) {
                console.error('Error updating featured status:', error);
                content.is_featured = originalValue;
            }
        },
        openModal(content) {
            axios.get(`/api/contents/${content.id}`)
                .then(response => {
                    this.selectedContent = response.data;
                })
                .catch(error => {
                    console.error('Error loading content details:', error);
                });
        },
        truncateText(text, length) {
            return text.length > length ? text.substring(0, length) + '...' : text;
        },
        handleImageUpload(event) {
            this.newContent.image = event.target.files[0];
        },
        async addContent() {
            try {
                const formData = new FormData();
                formData.append('title', this.newContent.title);
                formData.append('body', this.newContent.body);
                formData.append('is_featured', this.newContent.is_featured ? 1 : 0);
                if (this.newContent.image) {
                    formData.append('image', this.newContent.image);
                }

                await axios.post('/api/contents', formData, {
                    headers: { 'Content-Type': 'multipart/form-data' },
                });

                this.showAddModal = false;
                this.resetNewContent();
                await this.loadContents();
                await this.loadFeaturedContents();
            } catch (error) {
                console.error('Error adding content:', error);
            }
        },
        resetNewContent() {
            this.newContent = {
                title: '',
                body: '',
                image: null,
                is_featured: false,
            };
        },
        deleteContent(id) {
            if (confirm('Are you sure you want to delete this content?')) {
                axios.delete(`/api/contents/${id}`)
                    .then(() => {
                        this.contents = this.contents.filter(c => c.id !== id);
                        this.loadFeaturedContents();
                    })
                    .catch(error => {
                        console.error('Error deleting content:', error);
                        alert('Delete failed. Please check with programmer');
                    });
            }
        },
    },
};
</script>

<style scoped>
h2{
    color: #002739;
}

.carousel {
    background-color: #ff4343;
    padding: 2rem;
}

.carousel h2 {
    color: #fff;
    margin-bottom: 1rem;
}

.carousel-container {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding: 1rem 0 3rem 0;
}

.carousel-item {
    min-width: 300px;
    flex: 0 0 auto;
}

.carousel-card {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.3s;
    height: 100%;
}

.carousel-card:hover {
    transform: translateY(-5px);
}

.carousel-card img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.carousel-card h3 {
    margin: 0 0 0.5rem 0;
    color: #002739;
}

.carousel-card p {
    color: #666;
    margin: 0;
}

.content-actions {
    display: flex;
    justify-content: space-between;
    background-color: #33c8c8;
    padding: 1rem;
}

.filter-section {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: bold;
}

.btn-add {
    background-color: #002739;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.3s;
}

.btn-add:hover {
    color: #002739;
    background-color: #fff;
}

.btn-view {
    margin-right: 0.5rem;
}

.editable-table {
    width: 100%;
    border-collapse: collapse;
}

.editable-table th, .editable-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #ddd;
}

.editable-table th {
    background-color: #f5f5f5;
    color: #002739;
}

.editable-table tr:hover {
    background-color: #f9f9f9;
}

.btn-edit, .btn-save, .btn-delete, .btn-cancel {
    padding: 0.25rem 0.5rem;
    margin-right: 0.5rem;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.btn-edit {
    background-color: #3498db;
    color: white;
}

.btn-save {
    background-color: #2ecc71;
    color: white;
}

.btn-delete {
    background-color: #ff4343;
    color: white;
}

.btn-cancel {
    background-color: #95a5a6;
    color: white;
}

.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.modal-content {
    background-color: white;
    padding: 2rem;
    border-radius: 8px;
    width: 80%;
    max-width: 600px;
    max-height: 80vh;
    overflow-y: auto;
    position: relative;
}

.close {
    position: absolute;
    top: 1rem;
    right: 1rem;
    font-size: 1.5rem;
    cursor: pointer;
}

.modal-image {
    width: 100%;
    max-height: 300px;
    object-fit: cover;
    margin: 1rem 0;
    border-radius: 4px;
}

.content-stats {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
    color: #7f8c8d;
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #002739;
}

.form-group input, .form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.form-group textarea {
    min-height: 100px;
}

.btn-submit {
    background-color: #ff4343;
    color: white;
    border: none;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    margin-top: 1rem;
}

.btn-submit:hover {
    background-color: #002739
}

/* Update your modal form-group styles */
.form-group.checkbox-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group.checkbox-group label {
    margin-bottom: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-group.checkbox-group input[type="checkbox"] {
    margin: 0;
    width: auto;
}

.checkbox-wrapper {
    display: flex;
    justify-content: left;
    align-items: center;
    height: 100%;
}

.featured-checkbox {
    margin-left: 2rem;
}

.editable-table thead {
    background-color: #2c3e50;
    color: white;
}

.editable-table thead tr {
    background-color: #2c3e50;
    color: white;
}

.table-header th {
    background-color: #002739;
    color: #fff;
}

.inline-edit-text {
    cursor: pointer;
    padding: 2px 4px;
}
.inline-edit-text:hover {
    background-color: #f0f0f0;
}

.inline-edit-input {
    width: 100%;
    box-sizing: border-box;
    padding: 2px 4px;
    font-size: inherit;
}
</style>

<template>
  <div class="profiles">
    <div class="page-header">
      <h1>Professional Profiles</h1>
      <button @click="showCreateForm = true" class="btn btn-primary">Add New Profile</button>
    </div>

    <div v-if="loading" class="loading">Loading profiles...</div>

    <div v-else>
      <div v-if="profiles.length === 0" class="empty-state">
        <p>No profiles found. Create your first profile to get started!</p>
      </div>

      <div v-else class="grid grid-2">
        <div v-for="profile in profiles" :key="profile.id" class="card profile-card">
          <div class="profile-header">
            <div class="profile-image">
              <img v-if="profile.image_url" :src="profile.image_url" :alt="profile.name">
              <div v-else class="no-image">{{ profile.name.charAt(0) }}</div>
            </div>
            <div class="profile-info">
              <h3>{{ profile.name }}</h3>
              <p class="profession">{{ profile.profession }}</p>
              <p class="email">{{ profile.email }}</p>
            </div>
          </div>
          
          <div class="profile-bio" v-if="profile.bio">
            <p>{{ profile.bio.substring(0, 150) }}{{ profile.bio.length > 150 ? '...' : '' }}</p>
          </div>

          <div class="profile-links" v-if="hasLinks(profile)">
            <a v-if="profile.website_url" :href="profile.website_url" target="_blank" class="link">Website</a>
            <a v-if="profile.linkedin_url" :href="profile.linkedin_url" target="_blank" class="link">LinkedIn</a>
            <a v-if="profile.github_url" :href="profile.github_url" target="_blank" class="link">GitHub</a>
          </div>

          <div class="profile-actions">
            <router-link :to="`/profiles/${profile.id}`" class="btn btn-primary">View Details</router-link>
            <button @click="editProfile(profile)" class="btn btn-success">Edit</button>
            <button @click="deleteProfile(profile.id)" class="btn btn-danger">Delete</button>
          </div>

          <div class="video-count" v-if="profile.videos && profile.videos.length > 0">
            {{ profile.videos.length }} video{{ profile.videos.length !== 1 ? 's' : '' }}
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Profile Modal -->
    <div v-if="showCreateForm || editingProfile" class="modal-overlay" @click="closeForm">
      <div class="modal" @click.stop>
        <h2>{{ editingProfile ? 'Edit Profile' : 'Create New Profile' }}</h2>
        
        <form @submit.prevent="submitForm" class="profile-form">
          <div class="form-group">
            <label>Name *</label>
            <input v-model="form.name" type="text" required>
          </div>
          
          <div class="form-group">
            <label>Email *</label>
            <input v-model="form.email" type="email" required>
          </div>
          
          <div class="form-group">
            <label>Profession *</label>
            <input v-model="form.profession" type="text" required>
          </div>
          
          <div class="form-group">
            <label>Bio</label>
            <textarea v-model="form.bio" rows="3"></textarea>
          </div>
          
          <div class="form-group">
            <label>Image URL</label>
            <input v-model="form.image_url" type="url">
          </div>
          
          <div class="form-group">
            <label>LinkedIn URL</label>
            <input v-model="form.linkedin_url" type="url">
          </div>
          
          <div class="form-group">
            <label>GitHub URL</label>
            <input v-model="form.github_url" type="url">
          </div>
          
          <div class="form-group">
            <label>Website URL</label>
            <input v-model="form.website_url" type="url">
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ editingProfile ? 'Update' : 'Create' }}</button>
            <button type="button" @click="closeForm" class="btn">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { profilesAPI } from '../services/api.js'

export default {
  name: 'Profiles',
  data() {
    return {
      profiles: [],
      loading: true,
      showCreateForm: false,
      editingProfile: null,
      form: {
        name: '',
        email: '',
        profession: '',
        bio: '',
        image_url: '',
        linkedin_url: '',
        github_url: '',
        website_url: ''
      }
    }
  },
  async mounted() {
    await this.loadProfiles()
  },
  methods: {
    async loadProfiles() {
      try {
        this.loading = true
        const response = await profilesAPI.getAll()
        this.profiles = response.data
      } catch (error) {
        console.error('Error loading profiles:', error)
        alert('Error loading profiles')
      } finally {
        this.loading = false
      }
    },
    
    hasLinks(profile) {
      return profile.website_url || profile.linkedin_url || profile.github_url
    },
    
    editProfile(profile) {
      this.editingProfile = profile
      this.form = { ...profile }
    },
    
    closeForm() {
      this.showCreateForm = false
      this.editingProfile = null
      this.resetForm()
    },
    
    resetForm() {
      this.form = {
        name: '',
        email: '',
        profession: '',
        bio: '',
        image_url: '',
        linkedin_url: '',
        github_url: '',
        website_url: ''
      }
    },
    
    async submitForm() {
      try {
        if (this.editingProfile) {
          await profilesAPI.update(this.editingProfile.id, this.form)
        } else {
          await profilesAPI.create(this.form)
        }
        
        await this.loadProfiles()
        this.closeForm()
      } catch (error) {
        console.error('Error saving profile:', error)
        alert('Error saving profile')
      }
    },
    
    async deleteProfile(id) {
      if (!confirm('Are you sure you want to delete this profile?')) return
      
      try {
        await profilesAPI.delete(id)
        await this.loadProfiles()
      } catch (error) {
        console.error('Error deleting profile:', error)
        alert('Error deleting profile')
      }
    }
  }
}
</script>

<style scoped>
.page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 2rem;
}

.loading, .empty-state {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
}

.profile-card {
  position: relative;
}

.profile-header {
  display: flex;
  gap: 1rem;
  margin-bottom: 1rem;
}

.profile-image {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.profile-image img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-image {
  width: 100%;
  height: 100%;
  background: #3498db;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: bold;
}

.profile-info h3 {
  margin: 0;
  color: #2c3e50;
}

.profession {
  color: #3498db;
  font-weight: 500;
  margin: 0.25rem 0;
}

.email {
  color: #7f8c8d;
  font-size: 0.9rem;
  margin: 0;
}

.profile-bio {
  margin-bottom: 1rem;
  color: #555;
}

.profile-links {
  display: flex;
  gap: 0.5rem;
  margin-bottom: 1rem;
}

.link {
  background: #ecf0f1;
  color: #2c3e50;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  text-decoration: none;
  font-size: 0.8rem;
  transition: background-color 0.3s;
}

.link:hover {
  background: #d5dbdb;
}

.profile-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.profile-actions .btn {
  font-size: 0.8rem;
  padding: 0.4rem 0.8rem;
}

.video-count {
  position: absolute;
  top: 1rem;
  right: 1rem;
  background: #3498db;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.modal {
  background: white;
  border-radius: 8px;
  padding: 2rem;
  max-width: 500px;
  width: 90%;
  max-height: 90vh;
  overflow-y: auto;
}

.profile-form {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.form-group {
  display: flex;
  flex-direction: column;
}

.form-group label {
  margin-bottom: 0.25rem;
  font-weight: 500;
}

.form-group input,
.form-group textarea {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus {
  outline: none;
  border-color: #3498db;
}

.form-actions {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

@media (max-width: 768px) {
  .page-header {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
  
  .profile-header {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .profile-actions {
    justify-content: center;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>
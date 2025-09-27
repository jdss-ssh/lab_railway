<template>
  <div class="videos">
    <div class="page-header">
      <h1>Tutorial Videos</h1>
      <button @click="showCreateForm = true" class="btn btn-primary">Add New Video</button>
    </div>

    <div class="filters">
      <div class="filter-group">
        <label>Filter by:</label>
        <select v-model="selectedFilter" @change="applyFilters">
          <option value="all">All Videos</option>
          <option value="featured">Featured Only</option>
          <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
        </select>
      </div>
      
      <div class="filter-group">
        <label>Sort by:</label>
        <select v-model="sortBy" @change="applyFilters">
          <option value="created_at">Newest First</option>
          <option value="title">Title A-Z</option>
          <option value="duration">Duration</option>
        </select>
      </div>
    </div>

    <div v-if="loading" class="loading">Loading videos...</div>

    <div v-else>
      <div v-if="filteredVideos.length === 0" class="empty-state">
        <p>No videos found. Add your first video to get started!</p>
      </div>

      <div v-else class="grid grid-3">
        <div v-for="video in filteredVideos" :key="video.id" class="card video-card">
          <div class="video-thumbnail">
            <img v-if="video.thumbnail_url" :src="video.thumbnail_url" :alt="video.title">
            <div v-else class="no-thumbnail">No Thumbnail</div>
            <div class="video-overlay">
              <a :href="video.video_url" target="_blank" class="play-btn">▶</a>
            </div>
            <div v-if="video.is_featured" class="featured-badge">Featured</div>
          </div>

          <div class="video-info">
            <h3>{{ video.title }}</h3>
            <p class="video-description">{{ video.description.substring(0, 100) }}{{ video.description.length > 100 ? '...' : '' }}</p>
            
            <div class="video-meta">
              <div class="author-info" v-if="video.profile">
                <span class="author">By {{ video.profile.name }}</span>
                <span class="profession">{{ video.profile.profession }}</span>
              </div>
              
              <div class="video-details">
                <span v-if="video.duration" class="duration">{{ formatDuration(video.duration) }}</span>
                <span v-if="video.category" class="category">{{ video.category }}</span>
              </div>
            </div>

            <div class="video-actions">
              <a :href="video.video_url" target="_blank" class="btn btn-primary">Watch</a>
              <button @click="editVideo(video)" class="btn btn-success">Edit</button>
              <button @click="deleteVideo(video.id)" class="btn btn-danger">Delete</button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Create/Edit Video Modal -->
    <div v-if="showCreateForm || editingVideo" class="modal-overlay" @click="closeForm">
      <div class="modal" @click.stop>
        <h2>{{ editingVideo ? 'Edit Video' : 'Add New Video' }}</h2>
        
        <form @submit.prevent="submitForm" class="video-form">
          <div class="form-group">
            <label>Profile *</label>
            <select v-model="form.profile_id" required>
              <option value="">Select a profile</option>
              <option v-for="profile in profiles" :key="profile.id" :value="profile.id">
                {{ profile.name }} - {{ profile.profession }}
              </option>
            </select>
          </div>
          
          <div class="form-group">
            <label>Title *</label>
            <input v-model="form.title" type="text" required>
          </div>
          
          <div class="form-group">
            <label>Description *</label>
            <textarea v-model="form.description" rows="3" required></textarea>
          </div>
          
          <div class="form-group">
            <label>Video URL *</label>
            <input v-model="form.video_url" type="url" required>
          </div>
          
          <div class="form-group">
            <label>Thumbnail URL</label>
            <input v-model="form.thumbnail_url" type="url">
          </div>
          
          <div class="form-group">
            <label>Duration (seconds)</label>
            <input v-model.number="form.duration" type="number" min="1">
          </div>
          
          <div class="form-group">
            <label>Category</label>
            <input v-model="form.category" type="text" placeholder="e.g., Programming, Design, Marketing">
          </div>
          
          <div class="form-group">
            <label class="checkbox-label">
              <input v-model="form.is_featured" type="checkbox">
              Featured Video
            </label>
          </div>

          <div class="form-actions">
            <button type="submit" class="btn btn-primary">{{ editingVideo ? 'Update' : 'Create' }}</button>
            <button type="button" @click="closeForm" class="btn">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script>
import { videosAPI, profilesAPI } from '../services/api.js'

export default {
  name: 'Videos',
  data() {
    return {
      videos: [],
      profiles: [],
      filteredVideos: [],
      loading: true,
      showCreateForm: false,
      editingVideo: null,
      selectedFilter: 'all',
      sortBy: 'created_at',
      form: {
        profile_id: '',
        title: '',
        description: '',
        video_url: '',
        thumbnail_url: '',
        duration: null,
        category: '',
        is_featured: false
      }
    }
  },
  computed: {
    categories() {
      const cats = [...new Set(this.videos.map(v => v.category).filter(Boolean))]
      return cats.sort()
    }
  },
  async mounted() {
    await Promise.all([this.loadVideos(), this.loadProfiles()])
    this.applyFilters()
  },
  methods: {
    async loadVideos() {
      try {
        this.loading = true
        const response = await videosAPI.getAll()
        this.videos = response.data
      } catch (error) {
        console.error('Error loading videos:', error)
        alert('Error loading videos')
      } finally {
        this.loading = false
      }
    },
    
    async loadProfiles() {
      try {
        const response = await profilesAPI.getAll()
        this.profiles = response.data
      } catch (error) {
        console.error('Error loading profiles:', error)
      }
    },
    
    applyFilters() {
      let filtered = [...this.videos]
      
      // Apply filter
      if (this.selectedFilter === 'featured') {
        filtered = filtered.filter(v => v.is_featured)
      } else if (this.selectedFilter !== 'all') {
        filtered = filtered.filter(v => v.category === this.selectedFilter)
      }
      
      // Apply sort
      filtered.sort((a, b) => {
        if (this.sortBy === 'title') {
          return a.title.localeCompare(b.title)
        } else if (this.sortBy === 'duration') {
          return (b.duration || 0) - (a.duration || 0)
        } else { // created_at
          return new Date(b.created_at) - new Date(a.created_at)
        }
      })
      
      this.filteredVideos = filtered
    },
    
    formatDuration(seconds) {
      if (!seconds) return ''
      const mins = Math.floor(seconds / 60)
      const secs = seconds % 60
      return `${mins}:${secs.toString().padStart(2, '0')}`
    },
    
    editVideo(video) {
      this.editingVideo = video
      this.form = { ...video }
    },
    
    closeForm() {
      this.showCreateForm = false
      this.editingVideo = null
      this.resetForm()
    },
    
    resetForm() {
      this.form = {
        profile_id: '',
        title: '',
        description: '',
        video_url: '',
        thumbnail_url: '',
        duration: null,
        category: '',
        is_featured: false
      }
    },
    
    async submitForm() {
      try {
        const formData = { ...this.form }
        if (!formData.duration) delete formData.duration
        
        if (this.editingVideo) {
          await videosAPI.update(this.editingVideo.id, formData)
        } else {
          await videosAPI.create(formData)
        }
        
        await this.loadVideos()
        this.applyFilters()
        this.closeForm()
      } catch (error) {
        console.error('Error saving video:', error)
        alert('Error saving video')
      }
    },
    
    async deleteVideo(id) {
      if (!confirm('Are you sure you want to delete this video?')) return
      
      try {
        await videosAPI.delete(id)
        await this.loadVideos()
        this.applyFilters()
      } catch (error) {
        console.error('Error deleting video:', error)
        alert('Error deleting video')
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

.filters {
  display: flex;
  gap: 2rem;
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.filter-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.filter-group label {
  font-weight: 500;
}

.filter-group select {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.loading, .empty-state {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
}

.video-card {
  overflow: hidden;
  transition: transform 0.3s;
}

.video-card:hover {
  transform: translateY(-5px);
}

.video-thumbnail {
  position: relative;
  width: 100%;
  height: 200px;
  overflow: hidden;
}

.video-thumbnail img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.no-thumbnail {
  width: 100%;
  height: 100%;
  background: #ecf0f1;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #95a5a6;
}

.video-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0,0,0,0.3);
  display: flex;
  align-items: center;
  justify-content: center;
  opacity: 0;
  transition: opacity 0.3s;
}

.video-card:hover .video-overlay {
  opacity: 1;
}

.play-btn {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: rgba(255,255,255,0.9);
  color: #333;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  text-decoration: none;
  transition: transform 0.3s;
}

.play-btn:hover {
  transform: scale(1.1);
}

.featured-badge {
  position: absolute;
  top: 0.5rem;
  right: 0.5rem;
  background: #e74c3c;
  color: white;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.8rem;
  font-weight: bold;
}

.video-info {
  padding: 1rem;
}

.video-info h3 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.video-description {
  color: #555;
  margin-bottom: 1rem;
  line-height: 1.4;
}

.video-meta {
  margin-bottom: 1rem;
}

.author-info {
  margin-bottom: 0.5rem;
}

.author {
  font-weight: 500;
  color: #2c3e50;
}

.profession {
  color: #7f8c8d;
  font-size: 0.9rem;
  margin-left: 0.5rem;
}

.video-details {
  display: flex;
  gap: 1rem;
  align-items: center;
}

.duration {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.category {
  background: #3498db;
  color: white;
  padding: 0.2rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
}

.video-actions {
  display: flex;
  gap: 0.5rem;
  flex-wrap: wrap;
}

.video-actions .btn {
  font-size: 0.8rem;
  padding: 0.4rem 0.8rem;
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

.video-form {
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
.form-group textarea,
.form-group select {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
  font-family: inherit;
}

.form-group input:focus,
.form-group textarea:focus,
.form-group select:focus {
  outline: none;
  border-color: #3498db;
}

.checkbox-label {
  flex-direction: row !important;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-label input {
  width: auto;
  margin: 0;
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
  
  .filters {
    flex-direction: column;
    gap: 1rem;
  }
  
  .video-actions {
    justify-content: center;
  }
  
  .form-actions {
    flex-direction: column;
  }
}
</style>
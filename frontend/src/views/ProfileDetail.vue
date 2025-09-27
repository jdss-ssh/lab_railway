<template>
  <div class="profile-detail">
    <div v-if="loading" class="loading">Loading profile...</div>
    
    <div v-else-if="profile" class="profile-content">
      <!-- Profile Header -->
      <div class="profile-header">
        <div class="profile-image-large">
          <img v-if="profile.image_url" :src="profile.image_url" :alt="profile.name">
          <div v-else class="no-image">{{ profile.name.charAt(0) }}</div>
        </div>
        
        <div class="profile-info">
          <h1>{{ profile.name }}</h1>
          <h2 class="profession">{{ profile.profession }}</h2>
          <p class="email">{{ profile.email }}</p>
          
          <div class="profile-links" v-if="hasLinks(profile)">
            <a v-if="profile.website_url" :href="profile.website_url" target="_blank" class="btn btn-primary">
              🌐 Website
            </a>
            <a v-if="profile.linkedin_url" :href="profile.linkedin_url" target="_blank" class="btn btn-primary">
              💼 LinkedIn
            </a>
            <a v-if="profile.github_url" :href="profile.github_url" target="_blank" class="btn btn-primary">
              👨‍💻 GitHub
            </a>
          </div>
        </div>
      </div>

      <!-- Bio Section -->
      <div v-if="profile.bio" class="bio-section">
        <h3>About</h3>
        <p class="bio-text">{{ profile.bio }}</p>
      </div>

      <!-- Videos Section -->
      <div class="videos-section">
        <h3>Tutorial Videos ({{ videos.length }})</h3>
        
        <div v-if="videos.length === 0" class="empty-videos">
          <p>This profile doesn't have any videos yet.</p>
          <router-link to="/videos" class="btn btn-primary">Browse All Videos</router-link>
        </div>
        
        <div v-else class="videos-grid">
          <div v-for="video in videos" :key="video.id" class="video-card">
            <div class="video-thumbnail">
              <img v-if="video.thumbnail_url" :src="video.thumbnail_url" :alt="video.title">
              <div v-else class="no-thumbnail">No Thumbnail</div>
              <div class="video-overlay">
                <a :href="video.video_url" target="_blank" class="play-btn">▶</a>
              </div>
              <div v-if="video.is_featured" class="featured-badge">Featured</div>
            </div>

            <div class="video-info">
              <h4>{{ video.title }}</h4>
              <p class="video-description">{{ video.description }}</p>
              
              <div class="video-meta">
                <span v-if="video.duration" class="duration">{{ formatDuration(video.duration) }}</span>
                <span v-if="video.category" class="category">{{ video.category }}</span>
              </div>

              <div class="video-actions">
                <a :href="video.video_url" target="_blank" class="btn btn-primary">Watch Video</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div v-else class="error">
      <h2>Profile not found</h2>
      <p>The profile you're looking for doesn't exist.</p>
      <router-link to="/profiles" class="btn btn-primary">Back to Profiles</router-link>
    </div>
  </div>
</template>

<script>
import { profilesAPI, videosAPI } from '../services/api.js'

export default {
  name: 'ProfileDetail',
  props: {
    id: {
      type: String,
      required: true
    }
  },
  data() {
    return {
      profile: null,
      videos: [],
      loading: true
    }
  },
  async mounted() {
    await this.loadProfile()
  },
  watch: {
    id: {
      handler() {
        this.loadProfile()
      }
    }
  },
  methods: {
    async loadProfile() {
      try {
        this.loading = true
        
        // Load profile and videos concurrently
        const [profileResponse, videosResponse] = await Promise.all([
          profilesAPI.getById(this.id),
          videosAPI.getByProfile(this.id)
        ])
        
        this.profile = profileResponse.data
        this.videos = videosResponse.data
        
      } catch (error) {
        console.error('Error loading profile:', error)
        this.profile = null
      } finally {
        this.loading = false
      }
    },
    
    hasLinks(profile) {
      return profile.website_url || profile.linkedin_url || profile.github_url
    },
    
    formatDuration(seconds) {
      if (!seconds) return ''
      const mins = Math.floor(seconds / 60)
      const secs = seconds % 60
      return `${mins}:${secs.toString().padStart(2, '0')}`
    }
  }
}
</script>

<style scoped>
.loading {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
}

.error {
  text-align: center;
  padding: 2rem;
}

.error h2 {
  color: #e74c3c;
  margin-bottom: 1rem;
}

.profile-header {
  display: flex;
  gap: 2rem;
  margin-bottom: 3rem;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.profile-image-large {
  width: 150px;
  height: 150px;
  border-radius: 50%;
  overflow: hidden;
  flex-shrink: 0;
}

.profile-image-large img {
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
  font-size: 3rem;
  font-weight: bold;
}

.profile-info {
  flex: 1;
}

.profile-info h1 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
  font-size: 2.5rem;
}

.profession {
  color: #3498db;
  font-size: 1.5rem;
  margin: 0 0 0.5rem 0;
  font-weight: 500;
}

.email {
  color: #7f8c8d;
  margin: 0 0 1.5rem 0;
  font-size: 1.1rem;
}

.profile-links {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
}

.profile-links .btn {
  text-decoration: none;
}

.bio-section {
  margin-bottom: 3rem;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.bio-section h3 {
  margin: 0 0 1rem 0;
  color: #2c3e50;
}

.bio-text {
  line-height: 1.6;
  color: #555;
  font-size: 1.1rem;
}

.videos-section {
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.videos-section h3 {
  margin: 0 0 2rem 0;
  color: #2c3e50;
}

.empty-videos {
  text-align: center;
  padding: 2rem;
  color: #7f8c8d;
}

.videos-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
}

.video-card {
  border: 1px solid #ecf0f1;
  border-radius: 8px;
  overflow: hidden;
  transition: transform 0.3s;
}

.video-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 4px 8px rgba(0,0,0,0.1);
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

.video-info h4 {
  margin: 0 0 0.5rem 0;
  color: #2c3e50;
}

.video-description {
  color: #555;
  margin-bottom: 1rem;
  line-height: 1.4;
}

.video-meta {
  display: flex;
  gap: 1rem;
  align-items: center;
  margin-bottom: 1rem;
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

.video-actions .btn {
  font-size: 0.9rem;
  text-decoration: none;
}

@media (max-width: 768px) {
  .profile-header {
    flex-direction: column;
    align-items: center;
    text-align: center;
  }
  
  .profile-info h1 {
    font-size: 2rem;
  }
  
  .profession {
    font-size: 1.2rem;
  }
  
  .profile-links {
    justify-content: center;
  }
  
  .videos-grid {
    grid-template-columns: 1fr;
  }
}
</style>
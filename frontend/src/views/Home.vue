<template>
  <div class="home">
    <section class="hero">
      <h1>Welcome to Lab Railway</h1>
      <p>Discover professional profiles and tutorial videos from industry experts</p>
      <div class="hero-actions">
        <router-link to="/profiles" class="btn btn-primary">Browse Profiles</router-link>
        <router-link to="/videos" class="btn btn-success">Watch Videos</router-link>
      </div>
    </section>

    <section class="featured-section" v-if="featuredVideos.length > 0">
      <h2>Featured Videos</h2>
      <div class="grid grid-3">
        <div v-for="video in featuredVideos" :key="video.id" class="card video-card">
          <div class="video-thumbnail">
            <img v-if="video.thumbnail_url" :src="video.thumbnail_url" :alt="video.title">
            <div v-else class="no-thumbnail">No Thumbnail</div>
          </div>
          <h3>{{ video.title }}</h3>
          <p>{{ video.description.substring(0, 100) }}...</p>
          <div class="video-meta">
            <span class="author">By {{ video.profile?.name }}</span>
            <span class="category" v-if="video.category">{{ video.category }}</span>
          </div>
        </div>
      </div>
    </section>

    <section class="stats">
      <div class="grid grid-3">
        <div class="stat-card">
          <h3>{{ profileCount }}</h3>
          <p>Professional Profiles</p>
        </div>
        <div class="stat-card">
          <h3>{{ videoCount }}</h3>
          <p>Tutorial Videos</p>
        </div>
        <div class="stat-card">
          <h3>{{ featuredVideos.length }}</h3>
          <p>Featured Content</p>
        </div>
      </div>
    </section>
  </div>
</template>

<script>
import { profilesAPI, videosAPI } from '../services/api.js'

export default {
  name: 'Home',
  data() {
    return {
      featuredVideos: [],
      profileCount: 0,
      videoCount: 0,
      loading: true
    }
  },
  async mounted() {
    try {
      const [featuredResponse, profilesResponse, videosResponse] = await Promise.all([
        videosAPI.getFeatured(),
        profilesAPI.getAll(),
        videosAPI.getAll()
      ])
      
      this.featuredVideos = featuredResponse.data.slice(0, 6)
      this.profileCount = profilesResponse.data.length
      this.videoCount = videosResponse.data.length
    } catch (error) {
      console.error('Error loading home data:', error)
    } finally {
      this.loading = false
    }
  }
}
</script>

<style scoped>
.hero {
  text-align: center;
  padding: 4rem 0;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  border-radius: 8px;
  margin-bottom: 2rem;
}

.hero h1 {
  font-size: 3rem;
  margin-bottom: 1rem;
}

.hero p {
  font-size: 1.2rem;
  margin-bottom: 2rem;
}

.hero-actions {
  display: flex;
  gap: 1rem;
  justify-content: center;
  flex-wrap: wrap;
}

.featured-section {
  margin: 2rem 0;
}

.featured-section h2 {
  margin-bottom: 1rem;
  color: #2c3e50;
}

.video-card {
  transition: transform 0.3s;
}

.video-card:hover {
  transform: translateY(-5px);
}

.video-thumbnail {
  width: 100%;
  height: 150px;
  margin-bottom: 1rem;
  border-radius: 4px;
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

.video-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #7f8c8d;
}

.category {
  background: #3498db;
  color: white;
  padding: 0.2rem 0.5rem;
  border-radius: 12px;
  font-size: 0.8rem;
}

.stats {
  margin: 3rem 0;
}

.stat-card {
  text-align: center;
  padding: 2rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.stat-card h3 {
  font-size: 2.5rem;
  color: #3498db;
  margin-bottom: 0.5rem;
}

@media (max-width: 768px) {
  .hero h1 {
    font-size: 2rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
  }
}
</style>
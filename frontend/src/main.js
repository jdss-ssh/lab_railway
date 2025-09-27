import { createApp } from 'vue'
import { createRouter, createWebHistory } from 'vue-router'
import App from './App.vue'
import Home from './views/Home.vue'
import Profiles from './views/Profiles.vue'
import Videos from './views/Videos.vue'
import ProfileDetail from './views/ProfileDetail.vue'

const router = createRouter({
  history: createWebHistory(),
  routes: [
    { path: '/', name: 'home', component: Home },
    { path: '/profiles', name: 'profiles', component: Profiles },
    { path: '/profiles/:id', name: 'profile-detail', component: ProfileDetail, props: true },
    { path: '/videos', name: 'videos', component: Videos },
  ]
})

const app = createApp(App)
app.use(router)
app.mount('#app')
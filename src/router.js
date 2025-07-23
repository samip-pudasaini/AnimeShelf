import { createRouter, createWebHistory } from 'vue-router'
import Home from './views/Home.vue'
import News from './views/News.vue'
import About from './views/About.vue'
import AnimeList from './views/AnimeList.vue'
import Register from './views/Register.vue'
import Login from './views/Login.vue'
import UserList from './views/UserList.vue';

const routes = [
    { path: '/', component: Home },
    { path: '/news', component: News },
    { path: '/about', component: About },
    { path: '/animelist', component: AnimeList },
    { path: '/register', component: Register },
    { path: '/login', component: Login },
    { path: '/userlist', component: UserList },
]

const router = createRouter({
    history: createWebHistory("/cos30043/s104507107/Final_project/"),
    routes,
})

export default router
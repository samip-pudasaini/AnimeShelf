// App.vue
<template>
  <div>
    <nav class="navbar fixed-top navbar-expand-md navbar-dark bg-dark mb-4 pb-2">
      <div class="container-fluid">
        <a class="navbar-brand nav-link" href="/">AnimeShelf</a>
        <button
          class="navbar-toggler"
          type="button"
          data-bs-toggle="collapse"
          data-bs-target="#navbarNav"
          aria-controls="navbarNav"
          aria-expanded="false"
          aria-label="Toggle navigation"
        >
          <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ms-auto">
            <li class="nav-item">
              <router-link class="nav-link" to="/">Home</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/news">News</router-link>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/animelist">AnimeList</router-link>
            </li>
            <li v-if="!authStore.isAuthenticated" class="nav-item">
              <router-link class="nav-link" to="/register">Register</router-link>
            </li>
            <li v-if="!authStore.isAuthenticated" class="nav-item">
              <router-link class="nav-link" to="/login">Login</router-link>
            </li>
            <li v-if="authStore.isAuthenticated" class="nav-item">
              <router-link class="nav-link" to="/userlist">My List</router-link>
            </li>
            <li v-if="authStore.isAuthenticated" class="nav-item">
              <a class="nav-link" href="#" @click.prevent="logout">Logout</a>
            </li>
            <li class="nav-item">
              <router-link class="nav-link" to="/about">About</router-link>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <router-view></router-view>
  </div>
</template>

<script>
import { useAuthStore } from './stores/auth';

export default {
  name: 'App',
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  methods: {
    logout() {
      this.authStore.logout();
      this.$router.push('/login');
    },
  },
};
</script>

<style scoped>
.navbar-nav .nav-link {
  color: #ffffff;
}
.navbar-nav .nav-link:hover {
  color: #cccccc;
}
</style>
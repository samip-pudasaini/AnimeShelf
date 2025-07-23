<template>
  <div class="container py-4 mt-5">
    <h2 class="mb-2">Explore Anime</h2>

    <!--search and filter-->
    <div class="row mb-3">
      <div class="col-12 col-md-6">
        <input
          v-model="search"
          class="form-control"
          placeholder="Search anime..."
          aria-label="Search anime by title"
        />
      </div>
      <div class="col-12 col-md-6">
        <select
          v-model="genreFilter"
          class="form-select"
          aria-label="Filter by genre"
        >
          <option value="">All Genres</option>
          <option value="Action">Action</option>
          <option value="Adventure">Adventure</option>
          <option value="Sci-Fi">Sci-Fi</option>
          <option value="Fantasy">Fantasy</option>
          <option value="Drama">Drama</option>
          <option value="Sports">Sports</option>
        </select>
      </div>
    </div>

    <!--authentication-->
    <div v-if="!authStore.isAuthenticated" class="alert alert-warning my-4">
      <p>
        You need to <router-link to="/login">Login</router-link> to add anime to
        your list or like them.
      </p>
    </div>

    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-if="filteredAnime.length === 0 && !loading" class="alert alert-info">
      No anime found. Try adjusting your search or filter.
    </div>

    <!--anime display + pagination-->
    <div class="row">
      <div
        class="col-lg-4 col-sm-12 col-md-6"
        v-for="anime in paginatedAnime"
        :key="anime.mal_id"
      >
        <div class="card h-100">
          <img
            :src="anime.images.jpg.image_url"
            :alt="anime.title"
            class="card-img-top"
          />
          <div class="card-body">
            <h5 class="card-title">{{ anime.title }}</h5>
            <p class="card-text">Score: {{ anime.score || "N/A" }}</p>
            <p class="card-text">
              Likes:
              {{
                likeCounts && anime && anime.mal_id in likeCounts
                  ? likeCounts[anime.mal_id]
                  : 0
              }}
            </p>
            <p class="card-text">
              Genres: {{ anime.genres.map((g) => g.name).join(", ") || "N/A" }}
            </p>

            <!--like anime-->
            <button
              v-if="authStore.isAuthenticated"
              class="btn btn-outline-primary btn-sm me-2"
              @click="toggleLike(anime)"
              :disabled="likingAnime === anime.mal_id"
            >
              {{ userLikes.includes(Number(anime.mal_id)) ? "Unlike" : "Like" }}
            </button>

            <!--add anime to list-->
            <button
              v-if="authStore.isAuthenticated"
              class="btn btn-outline-success btn-sm"
              @click="addToList(anime)"
              :disabled="
                userAnime.some(
                  (item) => Number(item.anime_id) === Number(anime.mal_id)
                )
              "
              :title="
                userAnime.some(
                  (item) => Number(item.anime_id) === Number(anime.mal_id)
                )
                  ? 'Anime already in your list'
                  : 'Add to your list'
              "
            >
              {{
                userAnime.some(
                  (item) => Number(item.anime_id) === Number(anime.mal_id)
                )
                  ? "In List"
                  : "Add to My List"
              }}
            </button>
            <button v-else class="btn btn-outline-secondary btn-sm" disabled>
              Login to Add
            </button>
          </div>
        </div>
      </div>
    </div>

    <!--pagination pages-->
    <nav v-if="filteredAnime.length > 0 && !loading" class="mt-4">
      <ul class="pagination justify-content-center">
        <li class="page-item" :class="{ disabled: currentPage === 1 }">
          <button class="page-link" @click="prevPage">Previous</button>
        </li>
        <li
          v-for="page in totalPages"
          :key="page"
          class="page-item"
          :class="{ active: currentPage === page }"
        >
          <button class="page-link" @click="goToPage(page)">{{ page }}</button>
        </li>
        <li class="page-item" :class="{ disabled: currentPage === totalPages }">
          <button class="page-link" @click="nextPage">Next</button>
        </li>
      </ul>
    </nav>
  </div>
</template>

<script>
//using pinia
import { useAuthStore } from '../stores/auth';

export default {
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  data() {
    return {
      search: '',
      genreFilter: '',
      animeList: [],
      userLikes: [],
      loading: true,
      userAnime: [],
      likeCounts: {},
      likingAnime: null,
      currentPage: 1,
      itemsPerPage: 9,
    };
  },
  computed: {
    //filtering anime according to genre
    filteredAnime() {
      return this.animeList.filter((anime) => {
        const matchesSearch = anime.title.toLowerCase().includes(this.search.toLowerCase());
        const matchesGenre = this.genreFilter
          ? anime.genres.some((genre) => genre.name === this.genreFilter)
          : true;
        return matchesSearch && matchesGenre;
      });
    },
    //applying pagination
    paginatedAnime() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredAnime.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredAnime.length / this.itemsPerPage);
    },
  },
  methods: {
    //using external API for anime list
    async fetchAnime() {
      try {
        const response = await fetch('https://api.jikan.moe/v4/anime?limit=25');
        if (response.status === 429) throw new Error('Rate limit exceeded');
        const animeData = await response.json();
        this.animeList = animeData.data;
        this.loading = false;
      } catch (err) {
        console.error('Failed to fetch anime', err);
        this.loading = false;
        alert('Error fetching anime. Please try again later.');
      }
    },

    //fetching the backend data about anime in the user's list
    async fetchUserAnime() {
      this.loading = true;
      try {
        const userId = this.authStore.userId;
        if (!userId) throw new Error('No user ID found');
        const response = await fetch('resource/get_user_anime.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: userId }),
        });
        if (!response.ok) throw new Error(`Server error ${response.status}`);
        const data = await response.json();
        if (data.success) {
          this.userAnime = data.anime.map((item) => ({
            ...item,
            anime_id: item.anime_id || item.mal_id,
            status: item.status || 'plan_to_watch',
          }));
        } else {
          console.error('API error:', data.message || 'Failed to fetch anime list');
          alert(data.message || 'Failed to fetch your anime list.');
        }
      } catch (err) {
        console.error('Fetch error:', err);
        alert('Error fetching anime list.');
      }
      this.loading = false;
    },
    //fetching the backend data about anime that the user's have liked and also the total number of likes from all players.
    async fetchUserLikes() {
      if (!this.authStore.isAuthenticated) return;
      try {
        const response = await fetch('resource/get_likes.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ user_id: this.authStore.userId }),
        });
        const data = await response.json();
        if (data.success) {
          this.userLikes = data.likes.map((like) => Number(like.anime_id));
          this.likeCounts = data.likeCounts || {};
        }
      } catch (err) {
        console.error('Error fetching likes:', err);
      }
    },

    //add anime to the user's list
    async addToList(anime) {
      if (!this.authStore.isAuthenticated) {
        this.$router.push('/login');
        return;
      }
      if (this.userAnime.some((item) => Number(item.anime_id) === Number(anime.mal_id))) {
        alert('This anime is already in your list.');
        return;
      }
      try {
        const userId = this.authStore.userId; //using pinia to access the userId 
        const response = await fetch('resource/add_to_list.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            user_id: userId,
            mal_id: anime.mal_id,
            status: 'plan_to_watch',
          }),
        });
        const data = await response.json();
        if (data.success) {
          this.userAnime.push({
            anime_id: anime.mal_id,
            status: 'plan_to_watch',
          });
        } else {
          alert(data.message || 'Failed to add anime to your list.');
        }
      } catch (err) {
        alert('Error adding anime to your list: ' + err.message);
      }
    },

    //like the anime, show the change in button, and send data to the backend
    async toggleLike(anime) {
      if (!this.authStore.isAuthenticated) {
        this.$router.push('/login');
        return;
      }
      this.likingAnime = anime.mal_id;
      try {
        const isLiked = this.userLikes.includes(anime.mal_id);
        const response = await fetch('resource/like_anime.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            user_id: this.authStore.userId,
            anime_id: anime.mal_id,
            action: isLiked ? 'unlike' : 'like',
          }),
        });
        const data = await response.json();
        if (data.success) {
          if (data.liked) {
            this.userLikes.push(anime.mal_id);
            this.likeCounts[anime.mal_id] = (this.likeCounts[anime.mal_id] || 0) + 1;
          } else {
            this.userLikes = this.userLikes.filter((id) => id !== anime.mal_id);
            this.likeCounts[anime.mal_id] = Math.max((this.likeCounts[anime.mal_id] || 1) - 1, 0);
          }
        } else {
          alert(data.message || 'Failed to process like/unlike.');
        }
      } catch (err) {
        console.error('ToggleLike error:', err);
        alert('Error processing like/unlike.');
      } finally {
        this.likingAnime = null;
      }
    },

    //pagination move pages
    goToPage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    prevPage() {
      if (this.currentPage > 1) {
        this.currentPage--;
      }
    },
    nextPage() {
      if (this.currentPage < this.totalPages) {
        this.currentPage++;
      }
    },
  },
  created() {
    this.fetchAnime();
    if (this.authStore.isAuthenticated) {
      this.fetchUserAnime();
      this.fetchUserLikes();
    } else {
      this.loading = false;
    }
  },
  watch: {
    $route() { //fetches User likes if authenticated
      if (this.authStore.isAuthenticated) {
        this.fetchUserLikes();
      }
    },
    search() {
      this.currentPage = 1;
    },
    genreFilter() {
      this.currentPage = 1;
    },
  },
};
</script>

<style scoped>
.card-img-top {
  max-height: 300px;
  object-fit: cover;
}
.card {
  transition: transform 0.2s;
}
.card:hover {
  transform: translateY(-5px);
}
.pagination {
  margin-top: 20px;
}
.page-item.active .page-link {
  background-color: #007bff;
  border-color: #007bff;
  color: white;
}
.page-link {
  cursor: pointer;
}
</style>
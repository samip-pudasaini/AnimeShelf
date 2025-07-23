// UserList.vue
<template>
  <div class="container py-4 mt-5">
    <h2 class="mb-4">
      {{ username ? username + "'s Anime List" : "User's Anime List" }}
    </h2>

    <div v-if="authStore.isAuthenticated" class="mb-3">
      <button class="btn btn-outline-secondary" @click="logout">Logout</button>
    </div>

    <div v-if="!authStore.isAuthenticated" class="alert alert-warning">
      Please <router-link to="/login">login</router-link> to view your anime
      list.
    </div>

    <div v-if="loading" class="text-center my-4">
      <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
      </div>
    </div>

    <div v-if="userAnime.length === 0 && !loading" class="alert alert-info">
      Your anime list is empty. Add anime from the
      <router-link to="/animelist">Explore Anime</router-link> page.
    </div>

    <!--user anime list card-->
    <div v-else class="row">
      <div class="col-12 col-md-4 mb-3">
        <h4>Watching</h4>
        <!--making the list draggable-->
        <draggable
          v-model="watchingAnime"
          group="anime"
          @end="onDragEnd"
          class="card-list"
          item-key="anime_id"
          :ghost-class="'drag-ghost'"
        >
          <template #item="{ element: anime }">
            <div class="card mb-3">
              <div class="card-body">
                <h5 class="card-title">{{ anime.title }}</h5>
                <p class="card-text">
                  Status: {{ anime.status.replace("_", " ") }}
                </p>
                <!--update status if placed in another column-->
                <select
                  v-model="anime.status"
                  class="form-select w-auto d-inline-block"
                  @change="updateStatus(anime)"
                >
                  <option value="watching">Watching</option>
                  <option value="completed">Completed</option>
                  <option value="plan_to_watch">Plan to Watch</option>
                </select>
                <button
                  class="btn btn-outline-danger btn-sm ms-2"
                  @click="deleteAnime(anime)"
                >
                  Delete
                </button>
              </div>
            </div>
          </template>
          <template #header></template>
          <template #footer></template>
        </draggable>
      </div>

      <div class="col-12 col-md-4 mb-3">
        <h4>Completed</h4>
        <draggable
          v-model="completedAnime"
          group="anime"
          @end="onDragEnd"
          class="card-list"
          item-key="anime_id"
        >
          <template #item="{ element: anime }">
            <div class="card mb-3">
              <div class="card-body">
                <h5 class="card-title">{{ anime.title }}</h5>
                <p class="card-text">
                  Status: {{ anime.status.replace("_", " ") }}
                </p>
                <select
                  v-model="anime.status"
                  class="form-select w-auto d-inline-block"
                  @change="updateStatus(anime)"
                >
                  <option value="watching">Watching</option>
                  <option value="completed">Completed</option>
                  <option value="plan_to_watch">Plan to Watch</option>
                </select>
                <button
                  class="btn btn-outline-danger btn-sm ms-2"
                  @click="deleteAnime(anime)"
                >
                  Delete
                </button>
              </div>
            </div>
          </template>
          <template #header></template>
          <template #footer></template>
        </draggable>
      </div>

      <div class="col-12 col-md-4 mb-3">
        <h4>Plan to Watch</h4>
        <draggable
          v-model="planToWatchAnime"
          group="anime"
          @end="onDragEnd"
          class="card-list"
          item-key="anime_id"
        >
          <template #item="{ element: anime }">
            <div class="card mb-3">
              <div class="card-body">
                <h5 class="card-title">{{ anime.title }}</h5>
                <p class="card-text">
                  Status: {{ anime.status.replace("_", " ") }}
                </p>
                <select
                  v-model="anime.status"
                  class="form-select w-auto d-inline-block"
                  @change="updateStatus(anime)"
                >
                  <option value="watching">Watching</option>
                  <option value="completed">Completed</option>
                  <option value="plan_to_watch">Plan to Watch</option>
                </select>
                <button
                  class="btn btn-outline-danger btn-sm ms-2"
                  @click="deleteAnime(anime)"
                >
                  Delete
                </button>
              </div>
            </div>
          </template>
          <template #header></template>
          <template #footer></template>
        </draggable>
      </div>
    </div>
  </div>
</template>

<script>
import draggable from "vuedraggable"; //using the vuedraggable feature of vue js
import { useAuthStore } from "../stores/auth";

export default {
  components: {
    draggable,
  },
  setup() {
    //using pinia
    const authStore = useAuthStore();
    return { authStore };
  },
  data() {
    return {
      userAnime: [],
      loading: true,
      username: "",
    };
  },
  computed: {
    watchingAnime: {
      get() {
        return this.userAnime.filter((anime) => anime.status === "watching");
      },
      set(newList) {
        this.updateAnimeList(newList, "watching");
      },
    },
    completedAnime: {
      get() {
        return this.userAnime.filter((anime) => anime.status === "completed");
      },
      set(newList) {
        this.updateAnimeList(newList, "completed");
      },
    },
    planToWatchAnime: {
      get() {
        return this.userAnime.filter(
          (anime) => anime.status === "plan_to_watch"
        );
      },
      set(newList) {
        this.updateAnimeList(newList, "plan_to_watch");
      },
    },
  },
  methods: {
    async fetchUserAnime() {
      //fetch the user's anime from the backend
      this.loading = true;
      try {
        const userId = this.authStore.userId;
        if (!userId) throw new Error("No user ID found");
        const response = await fetch("resource/get_user_anime.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ user_id: userId }),
        });
        if (!response.ok) throw new Error(`Server error ${response.status}`);
        const data = await response.json();
        if (data.success) {
          this.username = data.username || "User"; // Set username here from API response
          this.userAnime = data.anime.map((item) => ({
            ...item,
            anime_id: item.anime_id || item.mal_id,
            status: item.status || "plan_to_watch",
          }));
        } else {
          console.error(
            "API error:",
            data.message || "Failed to fetch anime list"
          );
          alert(data.message || "Failed to fetch your anime list.");
        }
      } catch (err) {
        console.error("Fetch error:", err);
        alert("Error fetching anime list.");
      }
      this.loading = false;
    },

    //updating watch status
    async updateStatus(anime) {
      try {
        const userId = this.authStore.userId;
        if (!userId) throw new Error("No user ID found");
        const response = await fetch("resource/update_anime_status.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            user_id: userId,
            anime_id: anime.anime_id,
            status: anime.status,
          }),
        });
        const data = await response.json();
        if (!data.success) {
          alert(data.message || "Failed to update status.");
        }
      } catch (err) {
        alert("Error updating status.");
      }
    },

    //removing from the list
    async deleteAnime(anime) {
      try {
        const userId = this.authStore.userId;
        if (!userId) throw new Error("No user ID found");
        const response = await fetch("resource/delete_anime.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            user_id: userId,
            anime_id: anime.anime_id,
          }),
        });
        const data = await response.json();
        if (data.success) {
          this.userAnime = this.userAnime.filter(
            (a) => a.anime_id !== anime.anime_id
          );
        } else {
          alert(data.message || "Failed to remove anime.");
        }
      } catch (err) {
        alert("Error removing anime.");
      }
    },

    //logout
    logout() {
      this.authStore.logout();
      this.userAnime = [];
      this.$router.push("/login");
    },

    //update anime list accordingly
    updateAnimeList(newList, status) {
      const updatedAnime = newList.map((anime) => ({ ...anime, status }));
      this.userAnime = [
        ...this.userAnime.filter((a) => a.status !== status),
        ...updatedAnime,
      ];
      updatedAnime.forEach((anime) => this.updateStatus(anime));
    },

    //dragEnd event
    onDragEnd(event) {
      console.log("Drag ended:", event);
    },
  },
  created() {
    if (this.authStore.isAuthenticated) {
      this.fetchUserAnime();
    } else {
      this.loading = false;
    }
  },
};
</script>

<style scoped>
.card-list {
  min-height: 100px;
  padding: 10px;
  background-color: #f8f9fa;
  border-radius: 5px;
}

.drag-ghost{
  opacity: 0.6;
  background-color: chocolate;
  border: 1px dashed #0c5;
}
</style>

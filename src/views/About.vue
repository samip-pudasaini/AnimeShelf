<template>
  <div class="container mt-5">
    <h2>About this page</h2>
    <p>
      This is a Personal Anime wachlist tracker built with Vue 3 and styled by
      bootstrap
    </p>

    <input
      v-model="firstName"
      placeholder="First Name"
      class="form-control my-2"
    />
    <input
      v-model="lastName"
      placeholder="Last Name"
      class="form-control my-2"
    />
    <p>Welcome, {{ fullName }}!</p>

    <div>
      <input type="radio" id="shonen" value="Shonen" v-model="genre" />
      <label for="shonen">Shonen</label>
      <input type="radio" id="seinen" value="Seinen" v-model="genre" />
      <label for="seinen">Seinen</label>
    </div>
    <img v-if="genre" :src="genreImage" class="img-fluid mt-3" />
  </div>
</template>

<script>
  import shonenImg from "../assets/shonen.jpg";
  import seinenImg from "../assets/seinen.jpg";

export default {
  data() {
    return {
      firstName: "",
      lastName: "",
      genre: "",
    };
  },

  computed: {
    fullName() {
      return `${this.firstName} ${this.lastName}`;
    },

    genreImage() {
      return this.genre === "Shonen"
        ? shonenImg
        : seinenImg;
    },
  },
};
</script>

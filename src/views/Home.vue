<template>
   <div class="container text-center mt-5 px-3">
    <h1>Welcome to AnimeShelf</h1>
    <p>
      This is your personal anime wachlist tracker, where you can add from the
      list, your favourite anime in the list to track what you are watching,
      plan to watch or have completed watching. You can like the anime to give
      recommendation if you like the anime
    </p>

   <div class="row justify-content-center my-4">
      <div class="col-12 col-sm-6 col-md-4 mb-3">
        <img
          :src="writingImg"
          alt="Light Yagami writing in Death Note"
          class="img-fluid rounded shadow"
        />
      </div>
      <div class="col-12 col-sm-6 col-md-4 mb-3">
        <img
          :src="watchingImg"
          alt="Anime watching illustration"
          class="img-fluid rounded shadow"
        />
      </div>
    </div>

    <p>
        Add planned anime to watch in the list, then stay cool and watch it then update it as watching, then update to completed when finished watching.
    </p>
  </div>
</template>

<script>
import writingImg from "../assets/writing.jpg";
import watchingImg from "../assets/watchImage.jpg";

export default {
  data() {
    return {
      writingImg,
      watchingImg,
    };
  },
};
</script>

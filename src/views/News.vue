<template>
  <div class="container mt-5">
    <h2>Anime News</h2>
    <input
      v-model="search"
      class="form-control my-2"
      placeholder="Search news..."
    />

    <div v-for="item in paginatedNews" :key="item.id" class="border p-2 my-2">
      <h5>{{ item.title }}</h5>
      <small>{{ item.date }} - {{ item.category }}</small>
      <p>{{ item.content }}</p>
    </div>

    <button @click="prevPage" :disabled="page === 1">Prev</button>
    <button @click="nextPage" :disabled="page === pageCount">Next</button>
  </div>
</template>

<script>
import newsData from "../data/news.json";

export default {
  data() {
    return {
      search: "",
      page: 1,
      perpage: 3,
      news: newsData,
    };
  },

  computed: {
    filteredNews(){
        return this.news.filter(n => 
            [n.title, n.content, n.category, n.date].some(field =>
                field.toLowerCase().includes(this.search.toLowerCase())
            )
        )
    },
    
    pageCount(){
        return Math.ceil(this.filteredNews.length / this.perpage)
    },

    paginatedNews(){
        const start = (this.page - 1) * this.perpage
        return this.filteredNews.slice(start, start + this.perpage)
    }
  },

  methods: {
    nextPage() {
        if (this.page < this.pageCount){
            this.page++;
        }
    },

    prevPage(){
        if(this.page > 1){
            this.page--;
        }
    }
  }
};
</script>

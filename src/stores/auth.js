// stores/auth.js
import { defineStore } from 'pinia';

export const useAuthStore = defineStore('auth', {
  state: () => ({
    userToken: null,
    userId: null,
    username: null,
    isAuthenticated: false,
  }),
  actions: {
    login({ token, userId, username }) {
      this.userToken = token;
      this.userId = userId;
      this.username = username;
      this.isAuthenticated = true;
    },
    logout() {
      this.userToken = null;
      this.userId = null;
      this.username = null;
      this.isAuthenticated = false;
    },
  },
  persist: {
    storage: localStorage, // Persist to localStorage for session continuity
  },
});
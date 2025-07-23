// Login.vue
<template>
  <div class="container py-4 mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 col-lg-4">
        <h2 class="mb-4 text-center">Sign in to your account</h2>

        <div v-if="msg" class="alert alert-danger" role="alert">
          {{ msg }}
        </div>

        <form @submit.prevent="login">
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input
              v-model="input.username"
              id="username"
              class="form-control"
              placeholder="Enter username"
              required
            />
          </div>
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              v-model="input.password"
              id="password"
              type="password"
              class="form-control"
              placeholder="Enter password"
              required
            />
          </div>
          <button type="submit" class="btn btn-primary w-100">Sign in</button>
        </form>

        <p class="mt-3 text-center">
          Don't have an account?
          <router-link to="/register" class="text-primary">Sign up</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { useAuthStore } from '../stores/auth';

export default {
  name: 'Login',
  data() {
    return {
      msg: '',
      input: {
        username: '',
        password: '',
      },
    };
  },
  setup() {
    const authStore = useAuthStore();
    return { authStore };
  },
  methods: {
    //login, send data to backend
    async login() {
      if (this.validateForm()) {
        const requestOptions = {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            username: this.input.username,
            password: this.input.password,
          }),
        };

        try {
          const response = await fetch('resource/login.php', requestOptions);
          if (!response.ok) throw new Error(`Server error ${response.status}`);
          const data = await response.json();
          if (data.success) {
            this.authStore.login({
              token: data.token,
              userId: data.user_id,
              username: data.username, // Use username from response
            });
            this.input.username = '';
            this.input.password = '';
            this.msg = '';
            this.$router.push('/userlist');
          } else {
            this.msg = data.message || 'Login failed.';
          }
        } catch (error) {
          console.error('Fetch error:', error);
          this.msg = 'Login failed: ' + error.message;
        }
      }
    },
    
    //form validation
    validateForm() {
      if (!this.input.username && !this.input.password) {
        this.msg = 'Username and password are required';
        return false;
      }
      if (!this.input.username) {
        this.msg = 'Username is required';
        return false;
      }
      if (!this.input.password) {
        this.msg = 'Password is required';
        return false;
      }
      this.msg = '';
      return true;
    },
  },
};
</script>
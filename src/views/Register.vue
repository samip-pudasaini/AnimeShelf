<template>
  <div class="container py-4 mt-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-6 col-lg-4">
        <h2 class="mb-4">Register</h2>

        <div v-if="success" class="alert alert-success" role="alert">
          Registration successful! <router-link to="/login">Login</router-link>
        </div>

        <div v-if="error" class="alert alert-danger" role="alert">
          {{ error }}
        </div>

        <form @submit.prevent="register" v-if="!success">
          <!-- Username -->
          <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input
              v-model="username"
              id="username"
              class="form-control"
              placeholder="Enter username"
              required
              @blur="validateUsername"
              :class="{ 'is-invalid': usernameError }"
            />
            <div class="invalid-feedback" v-if="usernameError">{{ usernameError }}</div>
          </div>

          <!-- Email -->
          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input
              v-model="email"
              id="email"
              type="email"
              class="form-control"
              placeholder="Enter email"
              required
              @blur="validateEmail"
              :class="{ 'is-invalid': emailError }"
            />
            <div class="invalid-feedback" v-if="emailError">{{ emailError }}</div>
          </div>

          <!-- DOB -->
          <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <input
              v-model="dob"
              id="dob"
              type="date"
              class="form-control"
              required
              @blur="validateDob"
              :class="{ 'is-invalid': dobError }"
            />
            <div class="invalid-feedback" v-if="dobError">{{ dobError }}</div>
          </div>

          <!-- Password -->
          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input
              v-model="password"
              id="password"
              type="password"
              class="form-control"
              placeholder="Enter password"
              required
              @blur="validatePassword"
              :class="{ 'is-invalid': passwordError }"
            />
            <div class="invalid-feedback" v-if="passwordError">{{ passwordError }}</div>
          </div>

          <!-- Confirm Password -->
          <div class="mb-3">
            <label for="confirmPassword" class="form-label">Confirm Password</label>
            <input
              v-model="confirmPassword"
              id="confirmPassword"
              type="password"
              class="form-control"
              placeholder="Confirm password"
              required
              @blur="validateConfirmPassword"
              :class="{ 'is-invalid': confirmPasswordError }"
            />
            <div class="invalid-feedback" v-if="confirmPasswordError">{{ confirmPasswordError }}</div>
          </div>

          <!-- Submit -->
          <button
            type="submit"
            class="btn btn-primary w-100"
            :disabled="!isValid"
          >
            Register
          </button>
        </form>

        <!-- Only show the Login link if the registration is not successful -->
        <p class="mt-3 text-center" v-if="!success">
          Already have an account? <router-link to="/login">Login</router-link>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: "Register",

  data() {
    return {
      username: "",
      email: "",
      dob: "",
      password: "",
      confirmPassword: "",
      error: "",
      success: false,

      // Field-specific errors
      usernameError: "",
      emailError: "",
      dobError: "",
      passwordError: "",
      confirmPasswordError: "",
    };
  },

  computed: {
    isValid() {
      return (
        !this.usernameError &&
        !this.emailError &&
        !this.dobError &&
        !this.passwordError &&
        !this.confirmPasswordError &&
        this.username &&
        this.email &&
        this.dob &&
        this.password &&
        this.confirmPassword
      );
    },
  },

  methods: {
    validateUsername() {
      if (!this.username) {
        this.usernameError = "Username is required";
      } else if (this.username.length > 10) {
        this.usernameError = "Username must be ≤ 10 characters";
      } else {
        this.usernameError = "";
      }
    },

    validateEmail() {
      const pattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!this.email) {
        this.emailError = "Email is required";
      } else if (!pattern.test(this.email)) {
        this.emailError = "Please enter a valid email address";
      } else {
        this.emailError = "";
      }
    },

    validateDob() {
      if (!this.dob) {
        this.dobError = "Date of birth is required";
        return;
      }
      const birthDate = new Date(this.dob);
      const today = new Date();
      const age = today.getFullYear() - birthDate.getFullYear();
      const m = today.getMonth() - birthDate.getMonth();
      const isOldEnough =
        age > 16 || (age === 16 && (m > 0 || (m === 0 && today.getDate() >= birthDate.getDate())));

      this.dobError = isOldEnough ? "" : "You must be at least 16 years old";
    },

    validatePassword() {
      if (!this.password) {
        this.passwordError = "Password is required";
      } else if (this.password.length < 8) {
        this.passwordError = "Password must be at least 8 characters";
      } else {
        this.passwordError = "";
      }
    },

    validateConfirmPassword() {
      if (!this.confirmPassword) {
        this.confirmPasswordError = "Please confirm your password";
      } else if (this.password !== this.confirmPassword) {
        this.confirmPasswordError = "Passwords do not match";
      } else {
        this.confirmPasswordError = "";
      }
    },

    validateAllFields() {
      this.validateUsername();
      this.validateEmail();
      this.validateDob();
      this.validatePassword();
      this.validateConfirmPassword();
    },

    register() {
      this.error = "";
      this.validateAllFields();

      if (!this.isValid) {
        this.error = "Please fix the errors above before submitting.";
        return;
      }

      fetch("resource/register.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({
          username: this.username,
          password: this.password,
          email: this.email,
          dob: this.dob,
        }),
      })
        .then((res) => res.json())
        .then((data) => {
          if (data.success) {
            this.success = true;
          } else {
            this.error = data.message || "Registration failed.";
          }
        })
        .catch((err) => {
          this.error = "Error: " + err.message;
        });
    },
  },
};
</script>
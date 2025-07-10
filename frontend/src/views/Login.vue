<template>
  <div class="login-container">
    <!-- Background with subtle pattern -->
    <div class="login-background">
      <div class="background-pattern"></div>
    </div>

    <!-- Login Card -->
    <div class="login-card">
      <!-- Logo and Header -->
      <div class="login-header">
        <div class="logo-container">
          <div class="logo-icon">
            <svg
              class="w-8 h-8 text-white"
              fill="currentColor"
              viewBox="0 0 20 20"
            >
              <path
                d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"
              ></path>
            </svg>
          </div>
          <div class="logo-text">
            <h1 class="logo-title">BizTracker</h1>
            <p class="logo-subtitle">Business Management</p>
          </div>
        </div>

        <div class="welcome-text">
          <h2 class="welcome-title">Welcome back</h2>
          <p class="welcome-subtitle">Sign in to your account to continue</p>
        </div>
      </div>

      <!-- Login Form -->
      <form @submit.prevent="login" class="login-form">
        <!-- Email Input -->
        <div class="form-group">
          <label for="email" class="form-label">Email Address</label>
          <div class="input-wrapper">
            <div class="input-icon">
              <svg
                class="w-5 h-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"
                ></path>
              </svg>
            </div>
            <input
              id="email"
              v-model="email"
              type="email"
              placeholder="Enter your email"
              class="form-input"
              required
            />
          </div>
        </div>

        <!-- Password Input -->
        <div class="form-group">
          <label for="password" class="form-label">Password</label>
          <div class="input-wrapper">
            <div class="input-icon">
              <svg
                class="w-5 h-5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"
                ></path>
              </svg>
            </div>
            <input
              id="password"
              v-model="password"
              type="password"
              placeholder="Enter your password"
              class="form-input"
              required
            />
          </div>
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="form-options">
          <label class="checkbox-wrapper">
            <input type="checkbox" class="checkbox-input" />
            <span class="checkbox-label">Remember me</span>
          </label>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <!-- Error Message -->
        <div v-if="error" class="error-message">
          <div class="error-icon">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </div>
          <span>{{ error }}</span>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="login-button" :disabled="loading">
          <span v-if="!loading" class="button-content">
            <svg
              class="w-5 h-5 mr-2"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"
              ></path>
            </svg>
            Sign In
          </span>
          <span v-else class="loading-content">
            <svg
              class="animate-spin w-5 h-5 mr-2"
              fill="none"
              viewBox="0 0 24 24"
            >
              <circle
                class="opacity-25"
                cx="12"
                cy="12"
                r="10"
                stroke="currentColor"
                stroke-width="4"
              ></circle>
              <path
                class="opacity-75"
                fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
              ></path>
            </svg>
            Signing In...
          </span>
        </button>
      </form>

      <!-- Footer -->
      <div class="login-footer">
        <p class="footer-text">
          Don't have an account?
          <a href="#" class="signup-link">Sign up here</a>
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";

const email = ref("");
const password = ref("");
const error = ref("");
const loading = ref(false);
const router = useRouter();
// Inject the reactive isLoggedIn ref
const auth = inject("auth");

const login = async () => {
  loading.value = true;
  error.value = "";

  try {
    const response = await fetch("http://localhost:8000/api/auth/login_check", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ email: email.value, password: password.value }),
    });
    const data = await response.json();

    if (data.token) {
      localStorage.setItem("token", data.token);
      if (auth) auth.isLoggedIn.value = true; // ✅ now triggers reactivity
      router.push("/"); // or wherever your protected route is
    } else {
      error.value = "Invalid credentials";
    }
  } catch (e) {
    error.value = "Login failed. Please try again.";
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Login Container */
.login-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  padding: 1rem;
}

/* Background Pattern */
.login-background {
  position: absolute;
  inset: 0;
  overflow: hidden;
}

.background-pattern {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(
      circle at 25% 25%,
      rgba(255, 255, 255, 0.1) 0%,
      transparent 50%
    ),
    radial-gradient(
      circle at 75% 75%,
      rgba(255, 255, 255, 0.1) 0%,
      transparent 50%
    );
  background-size: 100px 100px;
  animation: float 20s ease-in-out infinite;
}

@keyframes float {
  0%,
  100% {
    transform: translateY(0px) rotate(0deg);
  }
  50% {
    transform: translateY(-20px) rotate(180deg);
  }
}

/* Login Card */
.login-card {
  background: white;
  border-radius: 1.5rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  padding: 3rem;
  width: 100%;
  max-width: 480px;
  position: relative;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Login Header */
.login-header {
  text-align: center;
  margin-bottom: 2.5rem;
}

.logo-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 2rem;
}

.logo-icon {
  width: 3.5rem;
  height: 3.5rem;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3);
}

.logo-text {
  text-align: left;
}

.logo-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
  line-height: 1.2;
}

.logo-subtitle {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
  font-weight: 500;
}

.welcome-title {
  font-size: 2rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0 0 0.5rem 0;
  line-height: 1.2;
}

.welcome-subtitle {
  font-size: 1rem;
  color: #6b7280;
  margin: 0;
  font-weight: 400;
}

/* Form Styles */
.login-form {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
}

.form-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.form-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #374151;
  margin-bottom: 0.25rem;
}

.input-wrapper {
  position: relative;
}

.input-icon {
  position: absolute;
  left: 1rem;
  top: 50%;
  transform: translateY(-50%);
  z-index: 10;
}

.form-input {
  width: 100%;
  padding: 1rem 1rem 1rem 3rem;
  border: 2px solid #f3f4f6;
  border-radius: 0.75rem;
  font-size: 1rem;
  background: #f9fafb;
  transition: all 0.2s ease;
  font-family: inherit;
}

.form-input:focus {
  outline: none;
  border-color: #3b82f6;
  background: white;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
}

/* Form Options */
.form-options {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 0.5rem 0;
}

.checkbox-wrapper {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-input {
  width: 1rem;
  height: 1rem;
  accent-color: #3b82f6;
  cursor: pointer;
}

.checkbox-label {
  font-size: 0.875rem;
  color: #4b5563;
  cursor: pointer;
}

.forgot-link {
  font-size: 0.875rem;
  color: #3b82f6;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
}

.forgot-link:hover {
  color: #1d4ed8;
  text-decoration: underline;
}

/* Error Message */
.error-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #fef2f2;
  border: 1px solid #fecaca;
  border-radius: 0.5rem;
  color: #dc2626;
  font-size: 0.875rem;
  font-weight: 500;
}

.error-icon {
  flex-shrink: 0;
}

/* Login Button */
.login-button {
  width: 100%;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  margin-top: 0.5rem;
}

.login-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

.login-button:active {
  transform: translateY(0);
}

.login-button:disabled {
  opacity: 0.7;
  cursor: not-allowed;
  transform: none;
}

.button-content,
.loading-content {
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Loading Animation */
.animate-spin {
  animation: spin 1s linear infinite;
}

@keyframes spin {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}

/* Footer */
.login-footer {
  text-align: center;
  margin-top: 2rem;
  padding-top: 2rem;
  border-top: 1px solid #f3f4f6;
}

.footer-text {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
}

.signup-link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.signup-link:hover {
  color: #1d4ed8;
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 640px) {
  .login-card {
    padding: 2rem 1.5rem;
    margin: 1rem;
    border-radius: 1rem;
  }

  .logo-container {
    flex-direction: column;
    gap: 0.5rem;
  }

  .logo-text {
    text-align: center;
  }

  .welcome-title {
    font-size: 1.75rem;
  }

  .form-options {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch;
  }
}

/* Focus and accessibility improvements */
.login-button:focus-visible,
.form-input:focus-visible,
.checkbox-input:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Smooth transitions for better UX */
* {
  transition: color 0.2s ease, background-color 0.2s ease,
    border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}
</style>

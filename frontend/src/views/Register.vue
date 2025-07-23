<template>
  <div class="register-container">
    <!-- Background with subtle pattern -->
    <div class="register-background">
      <div class="background-pattern"></div>
    </div>

    <!-- Register Card -->
    <div class="register-card">
      <!-- Logo and Header -->
      <div class="register-header">
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
          <h2 class="welcome-title">Create Account</h2>
          <p class="welcome-subtitle">
            Join BizTracker to manage your business
          </p>
        </div>
      </div>

      <!-- Register Form -->
      <form @submit.prevent="register" class="register-form">
        <!-- Full Name Input -->
        <div class="form-group">
          <label for="fullName" class="form-label">Full Name</label>
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
                  d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
                ></path>
              </svg>
            </div>
            <input
              id="fullName"
              v-model="fullName"
              type="text"
              placeholder="Enter your full name"
              class="form-input"
              required
            />
          </div>
        </div>

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
              placeholder="Create a password"
              class="form-input"
              required
            />
          </div>
        </div>

        <!-- Confirm Password Input -->
        <div class="form-group">
          <label for="confirmPassword" class="form-label"
            >Confirm Password</label
          >
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
                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"
                ></path>
              </svg>
            </div>
            <input
              id="confirmPassword"
              v-model="confirmPassword"
              type="password"
              placeholder="Confirm your password"
              class="form-input"
              required
            />
          </div>
        </div>

        <!-- Terms and Conditions -->
        <div class="form-options">
          <label class="checkbox-wrapper">
            <input
              v-model="agreeToTerms"
              type="checkbox"
              class="checkbox-input"
              required
            />
            <span class="checkbox-label">
              I agree to the
              <a href="#" class="terms-link">Terms of Service</a>
              and
              <a href="#" class="terms-link">Privacy Policy</a>
            </span>
          </label>
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

        <!-- Success Message -->
        <div v-if="success" class="success-message">
          <div class="success-icon">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
              <path
                fill-rule="evenodd"
                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                clip-rule="evenodd"
              ></path>
            </svg>
          </div>
          <span>{{ success }}</span>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="register-button"
          :disabled="loading || !isFormValid"
        >
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
                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"
              ></path>
            </svg>
            Create Account
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
            Creating Account...
          </span>
        </button>
      </form>

      <!-- Footer -->
      <div class="register-footer">
        <p class="footer-text">
          Already have an account?
          <router-link to="/login" class="signin-link"
            >Sign in here</router-link
          >
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { useRouter } from "vue-router";
import { inject } from "vue";

const fullName = ref("");
const email = ref("");
const password = ref("");
const confirmPassword = ref("");
const agreeToTerms = ref(false);
const error = ref("");
const success = ref("");
const loading = ref(false);
const router = useRouter();

// Inject the reactive isLoggedIn ref
const auth = inject("auth");

// Form validation
const isFormValid = computed(() => {
  return (
    fullName.value.trim() &&
    email.value.trim() &&
    password.value.length >= 6 &&
    confirmPassword.value === password.value &&
    agreeToTerms.value
  );
});

const validateForm = () => {
  error.value = "";

  if (!fullName.value.trim()) {
    error.value = "Full name is required";
    return false;
  }

  if (!email.value.trim()) {
    error.value = "Email is required";
    return false;
  }

  if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email.value)) {
    error.value = "Please enter a valid email address";
    return false;
  }

  if (password.value.length < 6) {
    error.value = "Password must be at least 6 characters long";
    return false;
  }

  if (password.value !== confirmPassword.value) {
    error.value = "Passwords do not match";
    return false;
  }

  if (!agreeToTerms.value) {
    error.value = "You must agree to the Terms of Service and Privacy Policy";
    return false;
  }

  return true;
};

const register = async () => {
  if (!validateForm()) return;

  loading.value = true;
  error.value = "";
  success.value = "";

  try {
    // Since there's no registration endpoint in your backend yet,
    // I'll simulate the registration process
    const response = await fetch("http://localhost:8000/api/auth/register", {
      method: "POST",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({
        name: fullName.value,
        email: email.value,
        password: password.value,
      }),
      credentials: "include", // ✅ VERY IMPORTANT if allow_credentials is true
    });

    if (response.ok) {
      const data = await response.json();
      success.value = "Account created successfully! Redirecting to login...";

      // Redirect to login after 2 seconds
      setTimeout(() => {
        router.push("/login");
      }, 2000);
    } else {
      const errorData = await response.json();
      error.value =
        errorData.message || "Registration failed. Please try again.";
    }
  } catch (e) {
    // For now, since the endpoint doesn't exist, we'll show a message
    error.value =
      "Registration endpoint not yet implemented. Please contact support.";
    console.error("Registration error:", e);
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
/* Register Container */
.register-container {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  padding: 1rem;
}

/* Background Pattern */
.register-background {
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

/* Register Card */
.register-card {
  background: white;
  border-radius: 1.5rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  padding: 3rem;
  width: 100%;
  max-width: 520px;
  position: relative;
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Register Header */
.register-header {
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
  background: linear-gradient(135deg, #10b981, #059669);
  border-radius: 1rem;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
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
.register-form {
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
  border-color: #10b981;
  background: white;
  box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.form-input::placeholder {
  color: #9ca3af;
}

/* Form Options */
.form-options {
  margin: 0.5rem 0;
}

.checkbox-wrapper {
  display: flex;
  align-items: flex-start;
  gap: 0.5rem;
  cursor: pointer;
}

.checkbox-input {
  width: 1rem;
  height: 1rem;
  accent-color: #10b981;
  cursor: pointer;
  margin-top: 0.125rem;
  flex-shrink: 0;
}

.checkbox-label {
  font-size: 0.875rem;
  color: #4b5563;
  cursor: pointer;
  line-height: 1.4;
}

.terms-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 500;
  transition: color 0.2s ease;
}

.terms-link:hover {
  color: #059669;
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

/* Success Message */
.success-message {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem 1rem;
  background: #f0fdf4;
  border: 1px solid #bbf7d0;
  border-radius: 0.5rem;
  color: #166534;
  font-size: 0.875rem;
  font-weight: 500;
}

.success-icon {
  flex-shrink: 0;
}

/* Register Button */
.register-button {
  width: 100%;
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-size: 1rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
  margin-top: 0.5rem;
}

.register-button:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
}

.register-button:active {
  transform: translateY(0);
}

.register-button:disabled {
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
.register-footer {
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

.signin-link {
  color: #10b981;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.2s ease;
}

.signin-link:hover {
  color: #059669;
  text-decoration: underline;
}

/* Responsive Design */
@media (max-width: 640px) {
  .register-card {
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

  .checkbox-wrapper {
    align-items: flex-start;
  }
}

/* Focus and accessibility improvements */
.register-button:focus-visible,
.form-input:focus-visible,
.checkbox-input:focus-visible {
  outline: 2px solid #10b981;
  outline-offset: 2px;
}

/* Smooth transitions for better UX */
* {
  transition: color 0.2s ease, background-color 0.2s ease,
    border-color 0.2s ease, transform 0.2s ease, box-shadow 0.2s ease;
}
</style>

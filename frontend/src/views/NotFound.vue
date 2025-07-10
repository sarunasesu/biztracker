<template>
  <div class="not-found-container">
    <!-- 404 Number -->
    <div class="error-number">
      <span class="number">404</span>
    </div>

    <!-- Error Message -->
    <div class="error-message">
      <h1 class="error-title">Oops! Page Not Found</h1>
      <p class="error-description">
        The page you're looking for seems to have wandered off into the digital
        void. Don't worry, it happens to the best of us!
      </p>
    </div>

    <!-- Action Button -->
    <div class="action-section">
      <button @click="goBack" class="back-button">
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
            d="M10 19l-7-7m0 0l7-7m-7 7h18"
          ></path>
        </svg>
        Go Back
      </button>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from "vue-router";

const router = useRouter();

const goBack = () => {
  if (window.history.length > 1) {
    router.go(-1);
  } else {
    router.push("/");
  }
};
</script>

<style scoped>
/* Container */
.not-found-container {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 2rem;
  background: #f8fafc;
  text-align: center;
}

/* 404 Number */
.error-number {
  margin-bottom: 2rem;
}

.number {
  font-size: 8rem;
  font-weight: 900;
  color: #3b82f6;
  line-height: 1;
  text-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
  animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
  0%,
  100% {
    opacity: 1;
  }
  50% {
    opacity: 0.8;
  }
}

/* Error Message */
.error-message {
  margin-bottom: 3rem;
  max-width: 600px;
}

.error-title {
  font-size: 2.5rem;
  font-weight: 700;
  color: #1f2937;
  margin-bottom: 1rem;
  line-height: 1.2;
}

.error-description {
  font-size: 1.125rem;
  color: #6b7280;
  line-height: 1.6;
  margin: 0;
}

/* Action Section */
.action-section {
  margin-top: 1rem;
}

.back-button {
  display: inline-flex;
  align-items: center;
  padding: 1rem 2rem;
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  border: none;
  border-radius: 0.75rem;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.2s ease;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
  text-decoration: none;
}

.back-button:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

.back-button:active {
  transform: translateY(0);
}

.back-button:focus-visible {
  outline: 2px solid #3b82f6;
  outline-offset: 2px;
}

/* Responsive Design */
@media (max-width: 768px) {
  .not-found-container {
    padding: 1.5rem;
  }

  .number {
    font-size: 6rem;
  }

  .error-title {
    font-size: 2rem;
  }

  .error-description {
    font-size: 1rem;
  }

  .back-button {
    width: 100%;
    max-width: 300px;
    justify-content: center;
  }
}

@media (max-width: 480px) {
  .number {
    font-size: 4rem;
  }

  .error-title {
    font-size: 1.75rem;
  }

  .back-button {
    padding: 0.875rem 1.5rem;
    font-size: 0.875rem;
  }
}

/* Smooth transitions */
* {
  transition: all 0.2s ease;
}
</style>

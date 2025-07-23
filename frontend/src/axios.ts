import axios from "axios";
import Swal from "sweetalert2";

const instance = axios.create({
  baseURL: "http://localhost:8000",
});

// Automatically attach token
instance.interceptors.request.use((config) => {
  const token = localStorage.getItem("token");
  if (token) {
    config.headers.Authorization = `Bearer ${token}`;
  }
  return config;
});

// Add 401 interceptor
instance.interceptors.response.use(
  (response) => response,
  async (error) => {
    if (error.response && error.response.status === 401) {
      localStorage.removeItem("token");

      const result = await Swal.fire({
        icon: "error",
        title: "Session Expired",
        text: "Your session has expired. Please log in again.",
        confirmButtonText: "Login",
        confirmButtonColor: "#3b82f6", // Tailwind blue-500
        backdrop: true,
        allowOutsideClick: false,
        allowEscapeKey: false,
      });

      // ✅ Only redirect if user confirms
      if (result.isConfirmed) {
        window.location.href = "/login";
      }
    }

    return Promise.reject(error);
  }
);

export default instance;

<script setup lang="ts">
import axios from "../axios"; // use your custom instance instead of the default axios
import { ref, computed, onMounted } from "vue";
import {
  XMarkIcon,
  PhotoIcon,
  CameraIcon,
  PlusIcon,
} from "@heroicons/vue/24/outline";

interface Props {
  isOpen: boolean;
}

interface Emits {
  (e: "close"): void;
  (e: "submit", product: any): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

interface Category {
  id: number;
  name: string;
}

const categories = ref<Category[]>([]);
const token = ref<string | null>(null);

onMounted(async () => {
  // Load token from localStorage (or sessionStorage)
  token.value = localStorage.getItem("token"); // or sessionStorage

  if (!token.value) {
    console.error("No token found. User might not be logged in.");
    return;
  }

  const cached = sessionStorage.getItem("categories");
  if (cached) {
    categories.value = JSON.parse(cached);
    return;
  }

  try {
    const res = await axios.get("/api/categories", {
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    });
    categories.value = res.data;
    sessionStorage.setItem("categories", JSON.stringify(res.data));
  } catch (err) {
    console.error("Failed to load categories:", err);
  }
});

const formData = ref({
  name: "",
  sku: "",
  photo: null as File | null,
  costPrice: "",
  valuePrice: "",
  soldPrice: "",
  stock: 1,
  comment: "",
  purchaseDate: new Date().toISOString().split("T")[0],
  soldDate: "",
  description: "",
  categoryId: "",
});

const showNewCategoryInput = ref(false);
const newCategoryName = ref("");
const photoPreview = ref<string | null>(null);
const errors = ref<Record<string, string>>({});
const isAddingCategory = ref(false);

const isFormValid = computed(() => {
  const f = formData.value;

  return (
    f.name.trim().length > 0 &&
    f.sku.trim().length > 0 &&
    f.categoryId !== "" &&
    f.purchaseDate !== "" &&
    f.costPrice !== "" &&
    f.valuePrice !== "" &&
    !isNaN(Number(f.costPrice)) &&
    !isNaN(Number(f.valuePrice)) &&
    Number(f.costPrice) >= 0 &&
    Number(f.valuePrice) >= 0
  );
});

const closeModal = () => {
  emit("close");
  resetForm();
};

const resetForm = () => {
  formData.value = {
    name: "",
    sku: "",
    photo: null,
    costPrice: "",
    valuePrice: "",
    soldPrice: "",
    stock: 1,
    comment: "",
    purchaseDate: new Date().toISOString().split("T")[0],
    soldDate: "",
    description: "",
    categoryId: "",
  };
  photoPreview.value = null;
  errors.value = {};
  showNewCategoryInput.value = false;
  newCategoryName.value = "";
};

const handlePhotoUpload = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];

  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      alert("File too large. Max size is 10MB.");
      return;
    }

    formData.value.photo = file;
    const reader = new FileReader();
    reader.onload = (e) => {
      photoPreview.value = e.target?.result as string;
    };
    reader.readAsDataURL(file);
  }
};

const removePhoto = () => {
  formData.value.photo = null;
  photoPreview.value = null;
};

const addNewCategory = async () => {
  const name = newCategoryName.value.trim();
  if (!name || !token.value) return;

  isAddingCategory.value = true; // Start loading

  try {
    const res = await axios.post(
      "/api/categories",
      { name },
      {
        headers: {
          Authorization: `Bearer ${token.value}`,
        },
      }
    );
    const newCategory = res.data;
    categories.value.push(newCategory);
    formData.value.categoryId = newCategory.id.toString();
    newCategoryName.value = "";
    showNewCategoryInput.value = false;
  } catch (err: any) {
    console.error("Failed to create category:", err?.response?.data || err);
  } finally {
    isAddingCategory.value = false; // End loading
  }
};

const validateForm = () => {
  errors.value = {};

  if (!formData.value.name) errors.value.name = "Product name is required";
  if (!formData.value.sku) errors.value.sku = "SKU is required";
  if (!formData.value.costPrice)
    errors.value.costPrice = "Cost price is required";
  if (!formData.value.valuePrice)
    errors.value.valuePrice = "Value price is required";
  if (!formData.value.categoryId)
    errors.value.categoryId = "Category is required";
  if (!formData.value.purchaseDate)
    errors.value.purchaseDate = "Purchase date is required";

  if (formData.value.costPrice && parseFloat(formData.value.costPrice) < 0)
    errors.value.costPrice = "Cost price must be positive";

  if (formData.value.valuePrice && parseFloat(formData.value.valuePrice) < 0)
    errors.value.valuePrice = "Value price must be positive";

  if (formData.value.soldPrice && parseFloat(formData.value.soldPrice) < 0)
    errors.value.soldPrice = "Sold price must be positive";

  return Object.keys(errors.value).length === 0;
};

const handleProductSubmit = async (productData: any) => {
  if (!token.value) return;
  try {
    const form = new FormData();
    for (const key in productData) {
      if (productData[key] !== null && productData[key] !== "") {
        form.append(key, productData[key]);
      }
    }

    const response = await axios.post("/api/products", form, {
      headers: {
        "Content-Type": "multipart/form-data",
        Authorization: `Bearer ${token.value}`,
      },
    });

    console.log("Saved:", response.data);

    // ✅ Emit the saved product back to the parent
    emit("submit", response.data);
  } catch (err: any) {
    console.error("Save failed:", err?.response?.data || err);
  }
};

const submitForm = () => {
  if (validateForm()) {
    handleProductSubmit(formData.value);
    closeModal();
  }
};
</script>

<template>
  <div v-if="isOpen" class="modal-overlay" @click="closeModal">
    <div class="modal-container" @click.stop>
      <!-- Modal Header -->
      <div class="modal-header">
        <h2 class="modal-title">Add New Product</h2>
        <button @click="closeModal" class="close-button">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- Modal Content -->
      <div class="modal-content">
        <form @submit.prevent="submitForm" class="product-form">
          <!-- Basic Information -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Product Name</label>
                <input
                  v-model="formData.name"
                  type="text"
                  placeholder="Enter product name"
                  class="form-input"
                  :class="{ error: errors.name }"
                />
                <span v-if="errors.name" class="error-text">{{
                  errors.name
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label required">SKU</label>
                <input
                  v-model="formData.sku"
                  type="text"
                  placeholder="Product SKU"
                  class="form-input"
                  :class="{ error: errors.sku }"
                />
                <span v-if="errors.sku" class="error-text">{{
                  errors.sku
                }}</span>
              </div>
            </div>
          </div>

          <!-- Product Photo -->
          <div class="form-section">
            <label class="form-label">Product Photos</label>
            <div class="photo-upload-area">
              <div v-if="!photoPreview" class="upload-placeholder">
                <PhotoIcon class="upload-icon" />
                <div class="upload-buttons">
                  <label class="upload-btn">
                    <input
                      type="file"
                      accept="image/*"
                      @change="handlePhotoUpload"
                      class="hidden"
                    />
                    Upload from Gallery
                  </label>
                  <button type="button" class="upload-btn secondary">
                    <CameraIcon class="w-4 h-4 mr-2" />
                    Take Photo
                  </button>
                </div>
              </div>

              <div v-else class="photo-preview">
                <img
                  :src="photoPreview"
                  alt="Product preview"
                  class="preview-image"
                />
                <button @click="removePhoto" type="button" class="remove-photo">
                  <XMarkIcon class="w-4 h-4" />
                </button>
              </div>
            </div>
          </div>

          <!-- Pricing -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Cost Price</label>
                <input
                  v-model="formData.costPrice"
                  type="number"
                  step="0.01"
                  placeholder="0.00"
                  class="form-input"
                  :class="{ error: errors.costPrice }"
                />
                <span v-if="errors.costPrice" class="error-text">{{
                  errors.costPrice
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label required">Value Price</label>
                <input
                  v-model="formData.valuePrice"
                  type="number"
                  step="0.01"
                  placeholder="0.00"
                  class="form-input"
                  :class="{ error: errors.valuePrice }"
                />
                <span v-if="errors.valuePrice" class="error-text">{{
                  errors.valuePrice
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label">Sold Price</label>
                <input
                  v-model="formData.soldPrice"
                  type="number"
                  step="0.01"
                  placeholder="Final sale price"
                  class="form-input"
                  :class="{ error: errors.soldPrice }"
                />
                <span v-if="errors.soldPrice" class="error-text">{{
                  errors.soldPrice
                }}</span>
              </div>
            </div>
          </div>

          <!-- Category and Stock -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Category</label>
                <div class="category-input-group">
                  <select
                    v-if="!showNewCategoryInput"
                    v-model="formData.categoryId"
                    class="form-input"
                    :class="{ error: errors.categoryId }"
                  >
                    <option value="">Select or create category</option>
                    <option
                      v-for="category in categories"
                      :key="category.id"
                      :value="category.id"
                    >
                      {{ category.name }}
                    </option>
                  </select>

                  <div v-else class="new-category-input">
                    <input
                      v-model="newCategoryName"
                      type="text"
                      placeholder="Enter new category name"
                      class="form-input"
                      @keyup.enter="addNewCategory"
                    />
                    <button
                      @click="addNewCategory"
                      type="button"
                      class="add-category-btn"
                      :disabled="isAddingCategory"
                    >
                      <template v-if="isAddingCategory"> Adding... </template>
                      <template v-else> Add </template>
                    </button>
                  </div>

                  <button
                    @click="showNewCategoryInput = !showNewCategoryInput"
                    type="button"
                    class="category-toggle-btn"
                  >
                    <PlusIcon class="w-4 h-4" />
                  </button>
                </div>
                <span v-if="errors.categoryId" class="error-text">{{
                  errors.categoryId
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label required">Current Stock</label>
                <input
                  v-model.number="formData.stock"
                  type="number"
                  min="0"
                  class="form-input"
                />
              </div>
            </div>
          </div>

          <!-- Dates -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Purchase Date</label>
                <input
                  v-model="formData.purchaseDate"
                  type="date"
                  class="form-input"
                  :class="{ error: errors.purchaseDate }"
                />
                <span v-if="errors.purchaseDate" class="error-text">{{
                  errors.purchaseDate
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label">Sold Date</label>
                <input
                  v-model="formData.soldDate"
                  type="date"
                  class="form-input"
                />
              </div>
            </div>
          </div>

          <!-- Additional Information -->
          <div class="form-section">
            <div class="form-group">
              <label class="form-label">Additional Comment</label>
              <textarea
                v-model="formData.comment"
                placeholder="Supplier, notes, etc."
                class="form-textarea"
                rows="3"
              ></textarea>
            </div>

            <div class="form-group">
              <label class="form-label">Description</label>
              <textarea
                v-model="formData.description"
                placeholder="Product description"
                class="form-textarea"
                rows="4"
              ></textarea>
            </div>
          </div>
        </form>
      </div>

      <!-- Modal Footer -->
      <div class="modal-footer">
        <button @click="closeModal" type="button" class="cancel-btn">
          Cancel
        </button>
        <button
          @click="submitForm"
          type="button"
          class="submit-btn"
          :disabled="!isFormValid"
        >
          Add Product
        </button>
      </div>
    </div>
  </div>
</template>

<style scoped>
/* Modal Overlay */
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  padding: 1rem;
}

/* Modal Container */
.modal-container {
  background: white;
  border-radius: 1rem;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}

/* Modal Header */
.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1.5rem 2rem;
  border-bottom: 1px solid #f3f4f6;
}

.modal-title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #1f2937;
  margin: 0;
}

.close-button {
  padding: 0.5rem;
  border: none;
  background: none;
  color: #6b7280;
  cursor: pointer;
  border-radius: 0.5rem;
  transition: all 0.2s ease;
}

.close-button:hover {
  background: #f3f4f6;
  color: #374151;
}

/* Modal Content */
.modal-content {
  flex: 1;
  overflow-y: auto;
  padding: 0 2rem;
}

/* Form Sections */
.form-section {
  margin-bottom: 2rem;
}

.form-row {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
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
}

.form-label.required::after {
  content: " *";
  color: #ef4444;
}

.form-input,
.form-textarea {
  padding: 0.75rem 1rem;
  border: 2px solid #f3f4f6;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  background: white;
  transition: all 0.2s ease;
  font-family: inherit;
}

.form-input:focus,
.form-textarea:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-input.error,
.form-textarea.error {
  border-color: #ef4444;
}

.form-input::placeholder,
.form-textarea::placeholder {
  color: #9ca3af;
}

.form-textarea {
  resize: vertical;
  min-height: 80px;
}

.error-text {
  font-size: 0.75rem;
  color: #ef4444;
  margin-top: 0.25rem;
}

/* Photo Upload */
.photo-upload-area {
  border: 2px dashed #d1d5db;
  border-radius: 0.75rem;
  padding: 2rem;
  text-align: center;
  transition: all 0.2s ease;
}

.photo-upload-area:hover {
  border-color: #3b82f6;
  background: #f8fafc;
}

.upload-placeholder {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 1rem;
}

.upload-icon {
  width: 3rem;
  height: 3rem;
  color: #9ca3af;
}

.upload-buttons {
  display: flex;
  gap: 1rem;
  flex-wrap: wrap;
  justify-content: center;
}

.upload-btn {
  display: inline-flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.5rem 1rem;
  background: #3b82f6;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  text-decoration: none;
}

.upload-btn:hover {
  background: #2563eb;
  transform: translateY(-1px);
}

.upload-btn.secondary {
  background: #6b7280;
}

.upload-btn.secondary:hover {
  background: #4b5563;
}

.hidden {
  display: none;
}

.photo-preview {
  position: relative;
  display: inline-block;
}

.preview-image {
  width: 150px;
  height: 150px;
  object-fit: cover;
  border-radius: 0.5rem;
  border: 2px solid #e5e7eb;
}

.remove-photo {
  position: absolute;
  top: -0.5rem;
  right: -0.5rem;
  background: #ef4444;
  color: white;
  border: none;
  border-radius: 50%;
  width: 2rem;
  height: 2rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  transition: all 0.2s ease;
}

.remove-photo:hover {
  background: #dc2626;
  transform: scale(1.1);
}

/* Category Input Group */
.category-input-group {
  display: flex;
  gap: 0.5rem;
  align-items: flex-start;
}

.category-input-group .form-input {
  flex: 1;
}

.new-category-input {
  display: flex;
  gap: 0.5rem;
  flex: 1;
}

.add-category-btn,
.category-toggle-btn {
  padding: 0.75rem;
  background: #10b981;
  color: white;
  border: none;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 0.875rem;
  font-weight: 500;
  white-space: nowrap;
}

.add-category-btn:hover,
.category-toggle-btn:hover {
  background: #059669;
  transform: translateY(-1px);
}

.category-toggle-btn {
  background: #6b7280;
  min-width: 2.75rem;
}

.category-toggle-btn:hover {
  background: #4b5563;
}

/* Modal Footer */
.modal-footer {
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
  padding: 1.5rem 2rem;
  border-top: 1px solid #f3f4f6;
}

.cancel-btn,
.submit-btn {
  padding: 0.75rem 1.5rem;
  border: none;
  border-radius: 0.5rem;
  font-weight: 600;
  font-size: 0.875rem;
  cursor: pointer;
  transition: all 0.2s ease;
}

.cancel-btn {
  background: #f3f4f6;
  color: #374151;
}

.cancel-btn:hover {
  background: #e5e7eb;
}

.submit-btn {
  background: linear-gradient(135deg, #3b82f6, #1d4ed8);
  color: white;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
}

.submit-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* Responsive Design */
@media (max-width: 768px) {
  .modal-container {
    margin: 0.5rem;
    max-height: 95vh;
  }

  .modal-header,
  .modal-content,
  .modal-footer {
    padding-left: 1rem;
    padding-right: 1rem;
  }

  .form-row {
    grid-template-columns: 1fr;
  }

  .upload-buttons {
    flex-direction: column;
  }

  .modal-footer {
    flex-direction: column;
  }

  .cancel-btn,
  .submit-btn {
    width: 100%;
  }
}
</style>

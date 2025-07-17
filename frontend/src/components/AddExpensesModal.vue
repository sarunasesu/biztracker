<script setup lang="ts">
import { ref, computed } from "vue";
import { XMarkIcon, PlusIcon } from "@heroicons/vue/24/outline";
import axios from "../axios";

interface Props {
  isOpen: boolean;
}

interface Emits {
  (e: "close"): void;
  (e: "submit", expenses: any): void;
}

const props = defineProps<Props>();
const emit = defineEmits<Emits>();

const token = ref<string | null>(localStorage.getItem("token"));

// Form data
const formData = ref({
  description: "",
  amount: "",
  category: "",
  date: new Date().toISOString().split("T")[0],
  paymentMethod: "",
  vendor: "",
  ReceiptNumber: "",
  notes: "",
});

// Categories
const categories = ["Rent", "Marketing", "Software", "Supplies", "Services"];

// Payment methods
const paymentMethods = ref([
  "Cash",
  "Credit Card",
  "Bank Transfer",
  "Check",
  "PayPal",
  "Other",
]);

const showNewCategoryInput = ref(false);
const newCategoryName = ref("");
const errors = ref<Record<string, string>>({});

// Computed
const isFormValid = computed(() => {
  return (
    formData.value.description &&
    formData.value.amount &&
    formData.value.category &&
    formData.value.date &&
    formData.value.paymentMethod
  );
});

// Methods
const closeModal = () => {
  emit("close");
  resetForm();
};

const resetForm = () => {
  formData.value = {
    description: "",
    amount: "",
    category: "",
    date: new Date().toISOString().split("T")[0],
    paymentMethod: "",
    vendor: "",
    ReceiptNumber: "",
    notes: "",
  };
  errors.value = {};
  showNewCategoryInput.value = false;
  newCategoryName.value = "";
};

const addNewCategory = () => {
  if (newCategoryName.value.trim()) {
    categories.value.push(newCategoryName.value.trim());
    formData.value.category = newCategoryName.value.trim();
    showNewCategoryInput.value = false;
    newCategoryName.value = "";
  }
};

const validateForm = () => {
  errors.value = {};

  if (!formData.value.description)
    errors.value.description = "Description is required";
  if (!formData.value.amount) errors.value.amount = "Amount is required";
  if (!formData.value.category) errors.value.category = "Category is required";
  if (!formData.value.date) errors.value.date = "Date is required";
  if (!formData.value.paymentMethod)
    errors.value.paymentMethod = "Payment method is required";

  if (formData.value.amount && parseFloat(formData.value.amount) <= 0) {
    errors.value.amount = "Amount must be greater than 0";
  }

  return Object.keys(errors.value).length === 0;
};

const handleexpensesSubmit = async (expensesData: any) => {
  if (!token.value) return;

  try {
    const payload = {
      ...expensesData,
      amount: parseFloat(expensesData.amount), // ensure it's a number
    };

    const response = await axios.post("/api/expenses", payload, {
      headers: {
        Authorization: `Bearer ${token.value}`,
      },
    });

    console.log("Saved expenses:", response.data);
    emit("submit", response.data); // optional: return created expenses
  } catch (err: any) {
    console.error("Failed to save expenses:", err?.response?.data || err);
  }
};

const submitForm = () => {
  if (validateForm()) {
    handleexpensesSubmit(formData.value);
    closeModal();
  }
};
</script>

<template>
  <div v-if="isOpen" class="modal-overlay" @click="closeModal">
    <div class="modal-container" @click.stop>
      <!-- Modal Header -->
      <div class="modal-header">
        <h2 class="modal-title">Add New Expenses</h2>
        <button @click="closeModal" class="close-button">
          <XMarkIcon class="w-6 h-6" />
        </button>
      </div>

      <!-- Modal Content -->
      <div class="modal-content">
        <form @submit.prevent="submitForm" class="expenses-form">
          <!-- Description and Amount -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Description</label>
                <input
                  v-model="formData.description"
                  type="text"
                  placeholder="expenses description"
                  class="form-input"
                  :class="{ error: errors.description }"
                />
                <span v-if="errors.description" class="error-text">{{
                  errors.description
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label required">Amount</label>
                <input
                  v-model="formData.amount"
                  type="number"
                  step="0.01"
                  placeholder="0.00"
                  class="form-input"
                  :class="{ error: errors.amount }"
                />
                <span v-if="errors.amount" class="error-text">{{
                  errors.amount
                }}</span>
              </div>
            </div>
          </div>

          <!-- Category, Date, and Payment Method -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label required">Category</label>
                <div class="category-input-group">
                  <select
                    v-if="!showNewCategoryInput"
                    v-model="formData.category"
                    class="form-input"
                    :class="{ error: errors.category }"
                  >
                    <option value="">Select category</option>
                    <option
                      v-for="category in categories"
                      :key="category"
                      :value="category"
                    >
                      {{ category }}
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
                    >
                      Add
                    </button>
                  </div>

                  <!-- <button
                    @click="showNewCategoryInput = !showNewCategoryInput"
                    type="button"
                    class="category-toggle-btn"
                  >
                    <PlusIcon class="w-4 h-4" />
                  </button> -->
                </div>
                <span v-if="errors.category" class="error-text">{{
                  errors.category
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label required">Date</label>
                <input
                  v-model="formData.date"
                  type="date"
                  class="form-input"
                  :class="{ error: errors.date }"
                />
                <span v-if="errors.date" class="error-text">{{
                  errors.date
                }}</span>
              </div>

              <div class="form-group">
                <label class="form-label required">Payment Method</label>
                <select
                  v-model="formData.paymentMethod"
                  class="form-input"
                  :class="{ error: errors.paymentMethod }"
                >
                  <option value="">Select payment method</option>
                  <option
                    v-for="method in paymentMethods"
                    :key="method"
                    :value="method"
                  >
                    {{ method }}
                  </option>
                </select>
                <span v-if="errors.paymentMethod" class="error-text">{{
                  errors.paymentMethod
                }}</span>
              </div>
            </div>
          </div>

          <!-- vendor and Receipt Number -->
          <div class="form-section">
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Vendor</label>
                <input
                  v-model="formData.vendor"
                  type="text"
                  placeholder="vendor name"
                  class="form-input"
                />
              </div>

              <div class="form-group">
                <label class="form-label">Receipt Number</label>
                <input
                  v-model="formData.ReceiptNumber"
                  type="text"
                  placeholder="Receipt #"
                  class="form-input"
                />
              </div>
            </div>
          </div>

          <!-- Notes -->
          <div class="form-section">
            <div class="form-group">
              <label class="form-label">Notes</label>
              <textarea
                v-model="formData.notes"
                placeholder="Additional notes"
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
          Add expenses
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
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
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

  .modal-footer {
    flex-direction: column;
  }

  .cancel-btn,
  .submit-btn {
    width: 100%;
  }
}
</style>

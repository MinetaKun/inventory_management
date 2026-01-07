<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">Stock In - Add Initial Stock</h2>
                <button
                    @click="$emit('close')"
                    class="text-gray-500 hover:text-gray-700 transition cursor-pointer"
                >
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form @submit.prevent="handleSubmit" class="p-6 space-y-6">
                <!-- Variant Selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Variant <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="formData.variant_id"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Select Variant</option>
                        <option v-for="variant in variants" :key="variant.id" :value="variant.id">
                            {{ variant.sku }} - {{ variant.inventory?.product_name }}
                            (Current Qty: {{ variant.quantity }})
                        </option>
                    </select>
                    <p v-if="errors.variant_id" class="mt-1 text-sm text-red-600">{{ errors.variant_id }}</p>
                </div>

                <!-- Location Selector -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Location <span class="text-red-500">*</span>
                    </label>
                    <select
                        v-model="formData.location_id"
                        required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">Select Location</option>
                        <option v-for="location in locations" :key="location.id" :value="location.id">
                            {{ location.name }}
                        </option>
                    </select>
                    <p v-if="errors.location_id" class="mt-1 text-sm text-red-600">{{ errors.location_id }}</p>
                </div>

                <!-- Quantity Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Quantity <span class="text-red-500">*</span>
                    </label>
                    <input
                        v-model.number="formData.quantity"
                        type="number"
                        min="1"
                        required
                        placeholder="Enter quantity"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                    <p v-if="errors.quantity" class="mt-1 text-sm text-red-600">{{ errors.quantity }}</p>
                </div>

                <!-- Notes (Optional) -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Notes (Optional)
                    </label>
                    <textarea
                        v-model="formData.notes"
                        rows="3"
                        placeholder="Add any notes about this stock in..."
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                    <p v-if="errors.notes" class="mt-1 text-sm text-red-600">{{ errors.notes }}</p>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition font-medium cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="isSubmitting"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ isSubmitting ? 'Adding Stock...' : 'Add Stock' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue'

const props = defineProps({
    variants: {
        type: Array,
        required: true,
    },
    locations: {
        type: Array,
        required: true,
    },
    initialVariantId: {
        type: [Number, String],
        default: '',
    },
})

const emit = defineEmits(['close', 'save'])

const formData = ref({
    variant_id: props.initialVariantId || '',
    location_id: '',
    quantity: 1,
    notes: '',
})

const errors = ref({})
const isSubmitting = ref(false)

const handleSubmit = async () => {
    errors.value = {}
    isSubmitting.value = true

    try {
        await emit('save', formData.value)
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>

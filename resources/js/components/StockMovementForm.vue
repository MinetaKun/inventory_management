<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center">
                <h2 class="text-2xl font-bold text-gray-900">New Stock Movement</h2>
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
                <!-- Action Type Toggle -->
                <div class="flex space-x-4 mb-4">
                    <button
                        type="button"
                        class="flex-1 py-2 px-4 rounded-lg font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                        :class="formData.to_location_id ? 'bg-blue-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        @click="setTransferType('location')"
                    >
                        Transfer Location
                    </button>
                    <button
                        type="button"
                        class="flex-1 py-2 px-4 rounded-lg font-medium transition focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500"
                        :class="!formData.to_location_id ? 'bg-purple-600 text-white shadow-md' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        @click="setTransferType('team')"
                    >
                        Issue to Team
                    </button>
                </div>

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
                        </option>
                    </select>
                    <p v-if="errors.variant_id" class="mt-1 text-sm text-red-600">{{ errors.variant_id }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- From Location -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            From Location <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="formData.from_location_id"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select Source</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id">
                                {{ location.name }}
                            </option>
                        </select>
                        <p v-if="errors.from_location_id" class="mt-1 text-sm text-red-600">{{ errors.from_location_id }}</p>
                    </div>

                    <!-- To Location (if Transfer) -->
                    <div v-if="actionType === 'location'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            To Location <span class="text-red-500">*</span>
                        </label>
                        <select
                            v-model="formData.to_location_id"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select Destination</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id" :disabled="location.id === formData.from_location_id">
                                {{ location.name }}
                            </option>
                        </select>
                        <p v-if="errors.to_location_id" class="mt-1 text-sm text-red-600">{{ errors.to_location_id }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Quantity -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Quantity <span class="text-red-500">*</span>
                        </label>
                        <input
                            v-model.number="formData.quantity"
                            type="number"
                            min="1"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <p v-if="errors.quantity" class="mt-1 text-sm text-red-600">{{ errors.quantity }}</p>
                    </div>

                    <!-- Due Date (if Issue to Team) -->
                    <div v-if="actionType === 'team'">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Due Date (Optional)
                        </label>
                        <input
                            v-model="formData.due_date"
                            type="datetime-local"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500"
                        />
                        <p class="text-xs text-gray-500 mt-1">Leave blank for permanent issue</p>
                        <p v-if="errors.due_date" class="mt-1 text-sm text-red-600">{{ errors.due_date }}</p>
                    </div>
                </div>

                <!-- Notes / Team Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        {{ actionType === 'team' ? 'Team Name / Reason' : 'Notes' }}
                    </label>
                    <textarea
                        v-model="formData.notes"
                        rows="3"
                        :placeholder="actionType === 'team' ? 'e.g. Marketing Team for Event X' : 'Additional details...'"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    ></textarea>
                </div>

                <div v-if="errors.general" class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                    {{ errors.general }}
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
                        class="px-6 py-2 text-white rounded-lg hover:opacity-90 transition font-medium cursor-pointer shadow-md hover:shadow-lg disabled:opacity-50 disabled:cursor-not-allowed"
                        :class="actionType === 'team' ? 'bg-purple-600' : 'bg-blue-600'"
                    >
                        {{ isSubmitting ? 'Processing...' : (actionType === 'team' ? 'Issue Stock' : 'Transfer Stock') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'

const props = defineProps({
    variants: { type: Array, required: true },
    locations: { type: Array, required: true },
    initialVariantId: { type: [Number, String], default: '' },
})

const emit = defineEmits(['close', 'save'])

const actionType = ref('location') // 'location' or 'team'
const isSubmitting = ref(false)
const errors = ref({})

const formData = ref({
    variant_id: props.initialVariantId || '', // Check if this prop is valid
    from_location_id: '',
    to_location_id: '',
    quantity: 1,
    notes: '',
    due_date: '',
    status: 'completed',
    assign_to_type: 'location',
})

const setTransferType = (type) => {
    actionType.value = type
    formData.value.assign_to_type = type
    
    if (type === 'team') {
        formData.value.to_location_id = '' // Clear destination for team
        formData.value.status = 'pending' // Default to pending for team issue
    } else {
        formData.value.status = 'completed' // Default to completed for transfer
        formData.value.due_date = '' // clear due date
    }
}

const handleSubmit = async () => {
    isSubmitting.value = true
    errors.value = {}

    try {
        await emit('save', formData.value)
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors
        } else if (error.response?.data?.message) {
            errors.value = { general: error.response.data.message }
        }
    } finally {
        isSubmitting.value = false
    }
}
</script>

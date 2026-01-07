<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="close">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md" @click.stop>
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ shelf ? 'Edit Shelf' : 'Add Shelf' }}
            </h2>

            <form @submit.prevent="submit">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Building *</label>
                        <select
                            v-model="form.location_id"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select Building</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id">
                                {{ location.name }}
                            </option>
                        </select>
                        <span v-if="errors.location_id" class="text-red-600 text-sm">{{ errors.location_id[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select
                            v-model="form.category_id"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select Category (Optional)</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <span v-if="errors.category_id" class="text-red-600 text-sm">{{ errors.category_id[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Shelf Name *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g., Shelf A"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.name" class="text-red-600 text-sm">{{ errors.name[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Code *</label>
                        <input
                            v-model="form.code"
                            type="text"
                            required
                            placeholder="e.g., MB-SA-01"
                            @input="form.code = form.code.toUpperCase()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.code" class="text-red-600 text-sm">{{ errors.code[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                        <textarea
                            v-model="form.description"
                            placeholder="Optional description"
                            rows="3"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                        <span v-if="errors.description" class="text-red-600 text-sm">{{ errors.description[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                        <select
                            v-model="form.status"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <span v-if="errors.status" class="text-red-600 text-sm">{{ errors.status[0] }}</span>
                    </div>
                </div>

                <div class="flex gap-3">
                    <button
                        type="button"
                        @click="close"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition font-medium cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer"
                    >
                        {{ shelf ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    shelf: {
        type: Object,
        default: null,
    },
    locations: {
        type: Array,
        required: true,
    },
    categories: {
        type: Array,
        required: true,
    },
})

const emit = defineEmits(['close', 'save'])

const form = ref({
    location_id: '',
    category_id: '',
    name: '',
    code: '',
    description: '',
    status: 'active',
})

const errors = ref({})

const initForm = () => {
    if (props.shelf) {
        form.value = {
            location_id: props.shelf.location_id,
            category_id: props.shelf.category_id || '',
            name: props.shelf.name,
            code: props.shelf.code,
            description: props.shelf.description || '',
            status: props.shelf.status,
        }
    } else {
        form.value = {
            location_id: '',
            category_id: '',
            name: '',
            code: '',
            description: '',
            status: 'active',
        }
    }
    errors.value = {}
}

watch(() => props.shelf, initForm, { immediate: true })

const close = () => {
    emit('close')
}

const submit = async () => {
    try {
        errors.value = {}
        
        const payload = {
            location_id: form.value.location_id,
            category_id: form.value.category_id || null,
            name: form.value.name,
            code: form.value.code,
            description: form.value.description || null,
            status: form.value.status,
        }

        emit('save', payload)
    } catch (error) {
        console.error('Submit error:', error)
    }
}
</script>

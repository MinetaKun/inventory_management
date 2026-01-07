<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg shadow-xl p-8 w-full max-w-md">
            <h2 class="text-2xl font-bold text-gray-900 mb-6">
                {{ category ? 'Edit Category' : 'Create Category' }}
            </h2>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Name</label>
                    <input
                        v-model="form.name"
                        type="text"
                        placeholder="e.g. Pet Collection"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        :class="errors.name ? 'border-red-500' : ''"
                        required
                    />
                    <p v-if="errors.name" class="text-red-600 text-sm mt-2">{{ errors.name[0] }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Code</label>
                    <input
                        v-model="form.code"
                        type="text"
                        placeholder="e.g. PET"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 uppercase"
                        :class="errors.code ? 'border-red-500' : ''"
                        required
                    />
                    <p v-if="errors.code" class="text-red-600 text-sm mt-2">{{ errors.code[0] }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status</label>
                    <select
                        v-model="form.status"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required
                    >
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                    <p v-if="errors.status" class="text-red-600 text-sm mt-2">{{ errors.status[0] }}</p>
                </div>

                <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                    <button
                        type="button"
                        @click="close"
                        class="px-4 py-2 bg-gray-200 text-gray-900 rounded-lg hover:bg-gray-300 font-medium transition"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition"
                    >
                        Save Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    category: {
        type: Object,
        default: null,
    },
})

const emit = defineEmits(['close', 'save'])

const isOpen = ref(false)
const form = ref({
    name: '',
    code: '',
    status: 'active',
})
const errors = ref({})

watch(
    () => props.category,
    (newCategory) => {
        if (newCategory) {
            form.value = {
                name: newCategory.name,
                code: newCategory.code,
                status: newCategory.status,
            }
        } else {
            form.value = {
                name: '',
                code: '',
                status: 'active',
            }
        }
        isOpen.value = true
    },
    { immediate: true }
)

const submit = async () => {
    errors.value = {}
    emit('save', form.value)
}

const close = () => {
    isOpen.value = false
    errors.value = {}
    form.value = {
        name: '',
        code: '',
        status: 'active',
    }
    emit('close')
}
</script>

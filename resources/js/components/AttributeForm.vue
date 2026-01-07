<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="close">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md" @click.stop>
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ attribute ? 'Edit Attribute' : 'Add Attribute' }}
            </h2>
            <form @submit.prevent="submit">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Name *</label>
                        <input
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="e.g., Size"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.name" class="text-red-600 text-sm">{{ errors.name[0] }}</span>
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
                    <button type="button" @click="close" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition font-medium cursor-pointer">Cancel</button>
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer">{{ attribute ? 'Update' : 'Create' }}</button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { ref, watch } from 'vue'
const props = defineProps({ attribute: { type: Object, default: null } })
const emit = defineEmits(['close', 'save'])
const form = ref({ name: '', status: 'active' })
const errors = ref({})
const initForm = () => {
    if (props.attribute) {
        form.value = { name: props.attribute.name, status: props.attribute.status }
    } else {
        form.value = { name: '', status: 'active' }
    }
    errors.value = {}
}
watch(() => props.attribute, initForm, { immediate: true })
const close = () => { emit('close') }
const submit = async () => { emit('save', { name: form.value.name, status: form.value.status }) }
</script>

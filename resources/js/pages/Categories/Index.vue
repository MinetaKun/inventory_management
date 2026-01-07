<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-6xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Categories</h1>
                <button
                    type="button"
                    @click="openAddModal"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg"
                >
                    + Add Category
                </button>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Code</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="category in categories" :key="category.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ category.name }}</td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded">
                                    {{ category.code }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm font-medium inline-block',
                                        category.status === 'active'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ category.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 space-x-3">
                                <button
                                    @click="editCategory(category)"
                                    class="text-blue-600 hover:text-blue-800 font-medium transition"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteCategory(category.id)"
                                    class="text-red-600 hover:text-red-800 font-medium transition"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="categories.length === 0">
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500">
                                No categories found. Create one to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <CategoryForm
            v-if="showModal"
            :category="selectedCategory"
            @close="closeModal"
            @save="saveCategory"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import CategoryForm from '@/components/CategoryForm.vue'

const categories = ref([])
const showModal = ref(false)
const selectedCategory = ref(null)

const fetchCategories = async () => {
    try {
        const response = await fetch('/api/categories')
        const data = await response.json()
        categories.value = data.data
    } catch (error) {
        console.error('Failed to fetch categories:', error)
    }
}

const openAddModal = () => {
    selectedCategory.value = null
    showModal.value = true
}

const editCategory = (category) => {
    selectedCategory.value = { ...category }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedCategory.value = null
}

const saveCategory = async (categoryData) => {
    try {
        const url = selectedCategory.value
            ? `/api/categories/${selectedCategory.value.id}`
            : '/api/categories'
        const method = selectedCategory.value ? 'PUT' : 'POST'

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(categoryData),
        })

        if (response.ok) {
            closeModal()
            await fetchCategories()
        } else {
            const error = await response.json()
            console.error('Save failed:', error)
        }
    } catch (error) {
        console.error('Failed to save category:', error)
    }
}

const deleteCategory = async (id) => {
    if (confirm('Are you sure you want to delete this category?')) {
        try {
            const response = await fetch(`/api/categories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })

            if (response.ok) {
                await fetchCategories()
            }
        } catch (error) {
            console.error('Failed to delete category:', error)
        }
    }
}

onMounted(() => {
    fetchCategories()
})
</script>

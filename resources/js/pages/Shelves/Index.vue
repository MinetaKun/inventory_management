<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Shelves</h1>
                <button
                    type="button"
                    @click="openAddModal"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg"
                >
                    + Add Shelf
                </button>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Building</label>
                        <select
                            v-model="filters.location_id"
                            @change="fetchShelves"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Buildings</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id">
                                {{ location.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select
                            v-model="filters.category_id"
                            @change="fetchShelves"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>


                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Code</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Building</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="shelf in shelves" :key="shelf.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ shelf.name }}</td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded">
                                    {{ shelf.code }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">{{ shelf.location?.name }}</td>
                            <td class="px-6 py-4 text-gray-700">
                                <span v-if="shelf.category">{{ shelf.category.name }}</span>
                                <span v-else class="text-gray-400 italic">—</span>
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm font-medium inline-block',
                                        shelf.status === 'active'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ shelf.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 space-x-3">
                                <button
                                    @click="editShelf(shelf)"
                                    class="text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteShelf(shelf.id)"
                                    class="text-red-600 hover:text-red-800 font-medium transition cursor-pointer"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="shelves.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No shelves found. Create one to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <ShelfForm
            v-if="showModal"
            :shelf="selectedShelf"
            :locations="locations"
            :categories="categories"
            @close="closeModal"
            @save="saveShelf"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import ShelfForm from '@/components/ShelfForm.vue'

const shelves = ref([])
const locations = ref([])
const categories = ref([])
const showModal = ref(false)
const selectedShelf = ref(null)
const filters = ref({
    location_id: '',
    category_id: '',
})

const fetchShelves = async () => {
    try {
        const params = new URLSearchParams()
        if (filters.value.location_id) params.append('location_id', filters.value.location_id)
        if (filters.value.category_id) params.append('category_id', filters.value.category_id)
        if (filters.value.layer) params.append('layer', filters.value.layer)

        const response = await fetch(`/api/shelves?${params}`)
        const data = await response.json()
        shelves.value = data.data
    } catch (error) {
        console.error('Failed to fetch shelves:', error)
    }
}

const fetchLocations = async () => {
    try {
        const response = await fetch('/api/locations')
        const data = await response.json()
        locations.value = data.data
    } catch (error) {
        console.error('Failed to fetch locations:', error)
    }
}

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
    selectedShelf.value = null
    showModal.value = true
}

const editShelf = (shelf) => {
    selectedShelf.value = { ...shelf }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedShelf.value = null
}

const saveShelf = async (shelfData) => {
    try {
        const url = selectedShelf.value
            ? `/api/shelves/${selectedShelf.value.id}`
            : '/api/shelves'
        const method = selectedShelf.value ? 'PUT' : 'POST'

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(shelfData),
        })

        if (response.ok) {
            closeModal()
            await fetchShelves()
        } else {
            const error = await response.json()
            console.error('Save failed:', error)
        }
    } catch (error) {
        console.error('Failed to save shelf:', error)
    }
}

const deleteShelf = async (id) => {
    if (confirm('Are you sure you want to delete this shelf?')) {
        try {
            const response = await fetch(`/api/shelves/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })

            if (response.ok) {
                await fetchShelves()
            }
        } catch (error) {
            console.error('Failed to delete shelf:', error)
        }
    }
}

onMounted(() => {
    fetchShelves()
    fetchLocations()
    fetchCategories()
})
</script>

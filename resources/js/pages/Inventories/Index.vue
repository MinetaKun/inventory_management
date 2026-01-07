<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Products</h1>
                <button
                    type="button"
                    @click="openAddModal"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg"
                >
                    + Add Product
                </button>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category</label>
                        <select
                            v-model="filters.category_id"
                            @change="fetchInventories"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Categories</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            v-model="filters.status"
                            @change="fetchInventories"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Statuses</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product Name</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Label</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">SKU Ref</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Category</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Cost Price</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="inventory in inventories" :key="inventory.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-gray-900 font-medium">{{ inventory.product_name }}</td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded">
                                    {{ inventory.product_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ inventory.sku_ref }}</td>
                            <td class="px-6 py-4 text-gray-700">{{ inventory.category?.name }}</td>
                            <td class="px-6 py-4 text-gray-900 font-semibold">${{ parseFloat(inventory.cost_price).toFixed(2) }}</td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm font-medium inline-block',
                                        inventory.status === 'active'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ inventory.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 space-x-3">
                                <button
                                    @click="editInventory(inventory)"
                                    class="text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteInventory(inventory.id)"
                                    class="text-red-600 hover:text-red-800 font-medium transition cursor-pointer"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="inventories.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No products found. Create one to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <InventoryForm
            v-if="showModal"
            :inventory="selectedInventory"
            :categories="categories"
            @close="closeModal"
            @save="saveInventory"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import InventoryForm from '@/components/InventoryForm.vue'

const inventories = ref([])
const categories = ref([])
const showModal = ref(false)
const selectedInventory = ref(null)
const filters = ref({
    category_id: '',
    status: '',
})

const fetchInventories = async () => {
    try {
        const params = new URLSearchParams()
        if (filters.value.category_id) params.append('category_id', filters.value.category_id)
        if (filters.value.status) params.append('status', filters.value.status)

        const response = await fetch(`/api/inventories?${params}`)
        const data = await response.json()
        inventories.value = data.data
    } catch (error) {
        console.error('Failed to fetch inventories:', error)
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
    selectedInventory.value = null
    showModal.value = true
}

const editInventory = (inventory) => {
    selectedInventory.value = { ...inventory }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedInventory.value = null
}

const saveInventory = async (inventoryData) => {
    try {
        const url = selectedInventory.value
            ? `/api/inventories/${selectedInventory.value.id}`
            : '/api/inventories'
        const method = selectedInventory.value ? 'PUT' : 'POST'

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(inventoryData),
        })

        if (response.ok) {
            closeModal()
            await fetchInventories()
        } else {
            const error = await response.json()
            console.error('Save failed:', error)
        }
    } catch (error) {
        console.error('Failed to save inventory:', error)
    }
}

const deleteInventory = async (id) => {
    if (confirm('Are you sure you want to delete this product?')) {
        try {
            const response = await fetch(`/api/inventories/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })

            if (response.ok) {
                await fetchInventories()
            }
        } catch (error) {
            console.error('Failed to delete inventory:', error)
        }
    }
}

onMounted(() => {
    fetchInventories()
    fetchCategories()
})
</script>

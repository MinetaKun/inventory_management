<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Stock In</h1>
                    <p class="text-gray-600 mt-1">Record initial stock entry for variants</p>
                </div>
                <button
                    type="button"
                    @click="openAddModal"
                    class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 active:bg-green-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg"
                >
                    + Add Stock In
                </button>
            </div>

            <!-- Recent Stock In Movements -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Stock In Records</h2>
                </div>
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">SKU</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Location</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Quantity</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Added By</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="movement in stockMovements" :key="movement.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ formatDate(movement.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded">
                                    {{ movement.variant?.sku }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                {{ movement.variant?.inventory?.product_name }}
                            </td>
                            <td class="px-6 py-4 text-gray-900">
                                {{ movement.location?.name }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-green-600">
                                    +{{ movement.quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-900">
                                {{ movement.moved_by_user?.name || 'N/A' }}
                            </td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm font-medium inline-block',
                                        movement.status === 'completed'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ movement.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="stockMovements.length === 0">
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No stock in records found. Add your first stock in to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <StockInForm
            v-if="showModal"
            :variants="variants"
            :locations="locations"
            @close="closeModal"
            @save="saveStockIn"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StockInForm from '@/components/StockInForm.vue'

const stockMovements = ref([])
const variants = ref([])
const locations = ref([])
const showModal = ref(false)

const fetchStockMovements = async () => {
    try {
        // Fetch recent stock in movements (type = 'in')
        const response = await fetch('/api/stock-movements?type=in')
        if (response.ok) {
            const data = await response.json()
            stockMovements.value = data.data || []
        }
    } catch (error) {
        console.error('Failed to fetch stock movements:', error)
    }
}

const fetchVariants = async () => {
    try {
        const response = await fetch('/api/variants')
        const data = await response.json()
        variants.value = data.data
    } catch (error) {
        console.error('Failed to fetch variants:', error)
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

const openAddModal = () => {
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
}

const saveStockIn = async (stockInData) => {
    try {
        const response = await fetch('/api/stock-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(stockInData),
        })

        if (response.ok) {
            closeModal()
            await fetchStockMovements()
            await fetchVariants() // Refresh to show updated quantities
            alert('Stock added successfully!')
        } else {
            const error = await response.json()
            console.error('Save failed:', error)
            alert(error.message || 'Failed to add stock')
        }
    } catch (error) {
        console.error('Failed to save stock in:', error)
        alert('Failed to add stock. Please try again.')
    }
}

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    const date = new Date(dateString)
    return date.toLocaleString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    })
}

onMounted(() => {
    fetchStockMovements()
    fetchVariants()
    fetchLocations()
})
</script>

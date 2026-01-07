<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Stock Movements</h1>
                    <p class="text-gray-600 mt-1">Track transfers, assignments, and returns</p>
                </div>
                <button
                    type="button"
                    @click="openAddModal"
                    class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg flex items-center gap-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                    New Movement
                </button>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-lg shadow p-4 mb-6">
                <div class="flex flex-wrap gap-4">
                    <select
                        v-model="filters.type"
                        @change="fetchMovements"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Types</option>
                        <option value="in">Stock In</option>
                        <option value="out">Stock Out/Issue</option>
                        <option value="transfer">Transfer</option>
                        <option value="return">Return</option>
                    </select>
                    
                    <select
                        v-model="filters.location_id"
                        @change="fetchMovements"
                        class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="">All Locations</option>
                        <option v-for="loc in locations" :key="loc.id" :value="loc.id">{{ loc.name }}</option>
                    </select>
                </div>
            </div>

            <!-- Movements Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Date/Time</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Type</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Item</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">From</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">To</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Qty</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Due Date</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="movement in movements" :key="movement.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ formatDate(movement.created_at) }}
                            </td>
                            <td class="px-6 py-4">
                                <span :class="getTypeBadgeClass(movement.type)">
                                    {{ formatType(movement.type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ movement.variant?.inventory?.product_name }}</div>
                                <div class="text-xs text-gray-500 font-mono">{{ movement.variant?.sku }}</div>
                            </td>
                            <td class="px-6 py-4 text-gray-900 text-sm">
                                {{ movement.from_location?.name || '-' }}
                            </td>
                            <td class="px-6 py-4 text-gray-900 text-sm">
                                {{ movement.to_location?.name || (movement.type === 'in' ? movement.location?.name : (movement.type === 'out' ? (movement.reason || 'External') : '-')) }}
                            </td>
                            <td class="px-6 py-4 font-semibold text-gray-900">
                                {{ movement.quantity }}
                            </td>
                            <td class="px-6 py-4 text-sm">
                                <span v-if="movement.due_date" :class="getDueDateClass(movement.due_date, movement.status)">
                                    {{ formatDate(movement.due_date, 'date') }}
                                </span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4">
                                <button
                                    v-if="canReturn(movement)"
                                    @click="returnStock(movement)"
                                    class="text-xs bg-orange-100 text-orange-700 px-2 py-1 rounded hover:bg-orange-200 transition font-medium cursor-pointer"
                                >
                                    Return
                                </button>
                                <span v-else-if="movement.status === 'returned'" class="text-xs text-green-600 font-medium">
                                    Returned
                                </span>
                            </td>
                        </tr>
                        <tr v-if="movements.length === 0">
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                No movements found matching your filters.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <StockMovementForm
            v-if="showModal"
            :variants="variants"
            :locations="locations"
            @close="closeModal"
            @save="saveMovement"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import StockMovementForm from '@/components/StockMovementForm.vue'

const movements = ref([])
const variants = ref([])
const locations = ref([])
const showModal = ref(false)
const filters = ref({
    type: '',
    location_id: '',
})

const fetchMovements = async () => {
    try {
        const params = new URLSearchParams()
        if (filters.value.type) params.append('type', filters.value.type)
        if (filters.value.location_id) params.append('location_id', filters.value.location_id)

        const response = await fetch(`/api/stock-movements?${params}`)
        const data = await response.json()
        movements.value = data.data
    } catch (error) {
        console.error('Failed to fetch movements:', error)
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

const saveMovement = async (data) => {
    try {
        const response = await fetch('/api/stock-movements', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        })

        if (!response.ok) {
            const error = await response.json()
            throw error
        }

        closeModal()
        await fetchMovements()
        alert('Stock movement recorded successfully')
    } catch (error) {
        console.error('Save failed:', error)
        throw { response: { data: error } } // Re-throw for component to handle errors
    }
}

const returnStock = async (movement) => {
    if (!confirm(`Confirm return of ${movement.quantity} items?`)) return

    try {
        const response = await fetch(`/api/stock-movements/${movement.id}/return`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })

        if (response.ok) {
            await fetchMovements()
            alert('Stock returned successfully')
        } else {
            const error = await response.json()
            alert(error.message || 'Failed to return stock')
        }
    } catch (error) {
        console.error('Return failed:', error)
        alert('Failed to process return')
    }
}

// Helpers
const formatDate = (dateString, type = 'datetime') => {
    if (!dateString) return ''
    const date = new Date(dateString)
    const options = type === 'date' 
        ? { year: 'numeric', month: 'short', day: 'numeric' }
        : { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' }
    return date.toLocaleString('en-US', options)
}

const getTypeBadgeClass = (type) => {
    const classes = {
        in: 'bg-green-100 text-green-800',
        out: 'bg-purple-100 text-purple-800',
        transfer: 'bg-blue-100 text-blue-800',
        return: 'bg-orange-100 text-orange-800',
    }
    return `px-2 py-1 rounded-full text-xs font-medium uppercase ${classes[type] || 'bg-gray-100'}`
}

const formatType = (type) => {
    const labels = {
        in: 'Stock In',
        out: 'Issued',
        transfer: 'Transfer',
        return: 'Returned',
    }
    return labels[type] || type
}

const getDueDateClass = (dateString, status) => {
    if (status === 'returned') return 'text-gray-400 line-through'
    const today = new Date()
    const due = new Date(dateString)
    return due < today ? 'text-red-600 font-bold' : 'text-gray-700'
}

const canReturn = (movement) => {
    return (movement.type === 'out') && movement.status !== 'returned' && movement.status !== 'cancelled'
}

onMounted(() => {
    fetchMovements()
    fetchVariants()
    fetchLocations()
})
</script>

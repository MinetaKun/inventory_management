<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-4xl mx-auto">
            <!-- Breadcrumbs / Back -->
            <div class="mb-6">
                <a href="/variants" class="text-blue-600 hover:underline flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Variants
                </a>
            </div>

            <div v-if="loading" class="text-center py-12">
                <svg class="animate-spin h-10 w-10 text-gray-400 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>

            <div v-else-if="variant" class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Main Info Card -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex justify-between items-start">
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ variant.inventory?.product_name || 'Details' }}</h1>
                                <p class="text-gray-500 font-mono text-lg mt-1">{{ variant.sku }}</p>
                            </div>
                            <span :class="getStatusClass(variant.status)" class="px-3 py-1 rounded-full text-sm font-medium capitalize">
                                {{ variant.status }}
                            </span>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4">
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <span class="text-gray-500 text-sm block">Total Quantity</span>
                                <span class="text-3xl font-bold text-blue-600">{{ variant.quantity }}</span>
                            </div>
                            <!-- Future: Add 'Available' vs 'Reserved' if we have reservation logic -->
                        </div>

                        <div class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Attributes</h3>
                            <div class="flex flex-wrap gap-2">
                                <span v-for="av in variant.attribute_values" :key="av.id" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-md text-sm border border-gray-200">
                                    <span class="font-bold text-gray-500">{{ av.attribute?.name }}:</span> {{ av.value }}
                                </span>
                            </div>
                        </div>

                        <div v-if="variant.note" class="mt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Notes</h3>
                            <p class="text-gray-600 text-sm bg-yellow-50 p-3 rounded-lg border border-yellow-100">
                                {{ variant.note }}
                            </p>
                        </div>
                    </div>

                    <!-- Recent Movements -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Recent Movements</h3>
                        <div v-if="history.length > 0" class="flow-root">
                            <ul class="-mb-8">
                                <li v-for="(movement, idx) in history" :key="movement.id">
                                    <div class="relative pb-8">
                                        <span v-if="idx !== history.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                        <div class="relative flex space-x-3">
                                            <div>
                                                <span :class="getMovementIconClass(movement.type)" class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white">
                                                    <!-- Icon based on type -->
                                                    <svg v-if="movement.type === 'in'" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                                    <svg v-else-if="movement.type === 'out'" class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/></svg>
                                                    <svg v-else class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                                </span>
                                            </div>
                                            <div class="min-w-0 flex-1 pt-1.5 flex justify-between space-x-4">
                                                <div>
                                                    <p class="text-sm text-gray-500">
                                                        <span class="font-medium text-gray-900 capitalize">{{ movement.type.replace('_', ' ') }}</span>
                                                        <span v-if="movement.notes"> - {{ movement.notes }}</span>
                                                    </p>
                                                    <p class="text-xs text-gray-400 mt-0.5">{{ formatDate(movement.created_at) }}</p>
                                                </div>
                                                <div class="text-right text-sm whitespace-nowrap text-gray-500">
                                                    <span class="font-bold" :class="movement.type === 'in' || movement.type === 'return' ? 'text-green-600' : 'text-red-600'">
                                                        {{ movement.type === 'in' || movement.type === 'return' ? '+' : '-' }}{{ movement.quantity }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <div class="mt-4 text-center">
                                <a href="/stock-movements" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View All History &rarr;</a>
                            </div>
                        </div>
                        <p v-else class="text-gray-500 text-sm text-center py-4">No recent movements.</p>
                    </div>
                </div>

                <!-- Sidebar (QR & Actions) -->
                <div class="space-y-6 order-first md:order-last">
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 text-center">Quick Actions</h3>
                        <div class="space-y-3">
                            <button @click="showStockInModal = true" class="block w-full text-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium transition cursor-pointer">
                                Stock In / Add
                            </button>
                            <button @click="showMovementModal = true" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition cursor-pointer">
                                Transfer / Issue
                            </button>
                        </div>
                    </div>

                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4 text-center">QR Code</h3>
                        <QrCodePreview :variant-id="variant.id" />
                    </div>
                </div>
            </div>
            
            <div v-else class="text-center py-12 text-red-600">
                Variant not found.
            </div>
        </div>

        <StockInForm
            v-if="showStockInModal"
            :variants="[variant]"
            :locations="locations"
            :initial-variant-id="variant.id"
            @close="showStockInModal = false"
            @save="saveStockIn"
        />

        <StockMovementForm
            v-if="showMovementModal"
            :variants="[variant]"
            :locations="locations"
            :initial-variant-id="variant.id"
            @close="showMovementModal = false"
            @save="saveMovement"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import QrCodePreview from '@/components/QrCodePreview.vue'
import StockInForm from '@/components/StockInForm.vue'
import StockMovementForm from '@/components/StockMovementForm.vue'

const props = defineProps({
    id: { type: [String, Number], required: true }
})

const variant = ref(null)
const history = ref([])
const locations = ref([]) // Needed for forms
const loading = ref(true)
const showStockInModal = ref(false)
const showMovementModal = ref(false)

const fetchVariant = async () => {
    try {
        const response = await fetch(`/api/variants/${props.id}`)
        const data = await response.json()
        variant.value = data.data
        await Promise.all([fetchHistory(), fetchLocations()])
    } catch (e) {
        console.error('Failed to fetch variant', e)
    } finally {
        loading.value = false
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

const fetchHistory = async () => {
    try {
        const response = await fetch(`/api/stock-movements?variant_id=${props.id}&type=`) 
        const data = await response.json()
        history.value = data.data.slice(0, 5) 
    } catch (e) {
        console.error('Failed to fetch history', e)
    }
}

const saveStockIn = async (data) => {
    try {
        const response = await fetch('/api/stock-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        })

        if (!response.ok) throw await response.json()

        showStockInModal.value = false
        // Refresh data
        await fetchVariant()
        alert('Stock added successfully')
    } catch (error) {
        console.error('Save failed:', error)
        throw { response: { data: error } } // Re-throw for component
    }
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

        if (!response.ok) throw await response.json()

        showMovementModal.value = false
        // Refresh data
        await fetchVariant()
        alert('Stock movement recorded successfully')
    } catch (error) {
        console.error('Save failed:', error)
        throw { response: { data: error } } // Re-throw for component
    }
}

const getStatusClass = (status) => {
    const map = {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-gray-100 text-gray-800',
        discontinued: 'bg-red-100 text-red-800',
        archived: 'bg-yellow-100 text-yellow-800',
    }
    return map[status] || 'bg-gray-100 text-gray-800'
}

const getMovementIconClass = (type) => {
    switch (type) {
        case 'in': return 'bg-green-500'
        case 'out': return 'bg-red-500'
        case 'return': return 'bg-green-600'
        default: return 'bg-blue-500'
    }
}

const formatDate = (date) => {
    return new Date(date).toLocaleString()
}

onMounted(() => {
    fetchVariant()
})
</script>

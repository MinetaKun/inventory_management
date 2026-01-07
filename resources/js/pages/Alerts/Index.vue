<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Inventory Alerts</h1>

            <!-- Tabs -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    <button
                        @click="currentTab = 'low_stock'"
                        :class="[
                            currentTab === 'low_stock'
                                ? 'border-red-500 text-red-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                        ]"
                    >
                        Low Stock Variants
                        <span v-if="lowStock.length" class="ml-2 bg-red-100 text-red-600 py-0.5 px-2.5 rounded-full text-xs">
                            {{ lowStock.length }}
                        </span>
                    </button>
                    <button
                        @click="currentTab = 'overdue'"
                        :class="[
                            currentTab === 'overdue'
                                ? 'border-purple-500 text-purple-600'
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                            'whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                        ]"
                    >
                        Overdue Returns
                        <span v-if="overdue.length" class="ml-2 bg-purple-100 text-purple-600 py-0.5 px-2.5 rounded-full text-xs">
                            {{ overdue.length }}
                        </span>
                    </button>
                </nav>
            </div>

            <!-- Low Stock Table -->
            <div v-if="currentTab === 'low_stock'" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">SKU</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Qty</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Min Stock</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="variant in lowStock" :key="variant.id" class="hover:bg-red-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ variant.inventory?.product_name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="text-sm font-mono text-gray-600">{{ variant.sku }}</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ variant.quantity }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ variant.min_stock }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">
                                {{ variant.status }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <Link :href="`/variants/${variant.id}`" class="text-indigo-600 hover:text-indigo-900">View</Link>
                            </td>
                        </tr>
                        <tr v-if="lowStock.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                All stock levels are healthy!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Overdue Table -->
            <div v-if="currentTab === 'overdue'" class="bg-white rounded-lg shadow overflow-hidden">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Item</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To (Team)</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Overdue By</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="movement in overdue" :key="movement.id" class="hover:bg-purple-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ movement.variant?.inventory?.product_name }}</div>
                                <div class="text-xs text-gray-500">{{ movement.variant?.sku }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ movement.from_location?.name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span v-if="movement.notes" class="font-medium text-purple-700">{{ movement.notes }}</span>
                                <span v-else class="text-gray-400">-</span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-medium">
                                {{ formatDate(movement.due_date) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                    {{ movement.days_overdue }} days
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="/stock-movements" class="text-indigo-600 hover:text-indigo-900">Manage</a>
                            </td>
                        </tr>
                        <tr v-if="overdue.length === 0">
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                No overdue returns!
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    lowStock: Array,
    overdue: Array,
})

const currentTab = ref('low_stock')

const formatDate = (dateString) => {
    if (!dateString) return '-'
    return new Date(dateString).toLocaleDateString()
}
</script>

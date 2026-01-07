<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold text-gray-900">Variants</h1>
                <div class="flex gap-3">
                    <a
                        href="/api/qr/download-all"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 active:bg-gray-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg flex items-center gap-2"
                        target="_blank"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Bulk QR
                    </a>
                    <button
                        type="button"
                        @click="openAddModal"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg"
                    >
                        + Add Variant
                    </button>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                        <select
                            v-model="filters.inventory_id"
                            @change="fetchVariants"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">All Products</option>
                            <option v-for="inventory in inventories" :key="inventory.id" :value="inventory.id">
                                {{ inventory.product_name }} ({{ inventory.product_label }})
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <select
                            v-model="filters.status"
                            @change="fetchVariants"
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
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">SKU</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Product</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Attributes</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Quantity</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Status</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr v-for="variant in variants" :key="variant.id" class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <span class="font-mono text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded">
                                    {{ variant.sku }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-medium">
                                {{ variant.inventory?.product_name }}
                                <div class="text-sm text-gray-500">{{ variant.inventory?.product_label }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    <span
                                        v-for="attrValue in variant.attribute_values"
                                        :key="attrValue.id"
                                        class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded"
                                    >
                                        {{ attrValue.attribute.name }}: {{ attrValue.value }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-900 font-semibold">{{ variant.quantity }}</td>
                            <td class="px-6 py-4">
                                <span
                                    :class="[
                                        'px-3 py-1 rounded-full text-sm font-medium inline-block',
                                        variant.status === 'active'
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ variant.status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 space-x-3">
                                <Link
                                    :href="`/variants/${variant.id}`"
                                    class="text-green-600 hover:text-green-800 font-medium transition cursor-pointer"
                                >
                                    Details
                                </Link>
                                <button
                                    @click="editVariant(variant)"
                                    class="text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer"
                                >
                                    Edit
                                </button>
                                <button
                                    @click="deleteVariant(variant.id)"
                                    class="text-red-600 hover:text-red-800 font-medium transition cursor-pointer"
                                >
                                    Delete
                                </button>
                            </td>
                        </tr>
                        <tr v-if="variants.length === 0">
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No variants found. Create one to get started.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <VariantForm
            v-if="showModal"
            :variant="selectedVariant"
            :inventories="inventories"
            :attributes="attributes"
            :attributeValues="attributeValues"
            @close="closeModal"
            @save="saveVariant"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'
import VariantForm from '@/components/VariantForm.vue'

const variants = ref([])
const inventories = ref([])
const attributes = ref([])
const attributeValues = ref([])
const showModal = ref(false)
const selectedVariant = ref(null)
const filters = ref({
    inventory_id: '',
    status: '',
})

const fetchVariants = async () => {
    try {
        const params = new URLSearchParams()
        if (filters.value.inventory_id) params.append('inventory_id', filters.value.inventory_id)
        if (filters.value.status) params.append('status', filters.value.status)

        const response = await fetch(`/api/variants?${params}`)
        const data = await response.json()
        variants.value = data.data
    } catch (error) {
        console.error('Failed to fetch variants:', error)
    }
}

const fetchInventories = async () => {
    try {
        const response = await fetch('/api/inventories')
        const data = await response.json()
        inventories.value = data.data
    } catch (error) {
        console.error('Failed to fetch inventories:', error)
    }
}

const fetchAttributes = async () => {
    try {
        const response = await fetch('/api/attributes')
        const data = await response.json()
        attributes.value = data.data
    } catch (error) {
        console.error('Failed to fetch attributes:', error)
    }
}

const fetchAttributeValues = async () => {
    try {
        const response = await fetch('/api/attribute-values')
        const data = await response.json()
        attributeValues.value = data.data
    } catch (error) {
        console.error('Failed to fetch attribute values:', error)
    }
}

const openAddModal = () => {
    selectedVariant.value = null
    showModal.value = true
}

const editVariant = (variant) => {
    selectedVariant.value = { ...variant }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedVariant.value = null
}

const saveVariant = async (variantData) => {
    try {
        const url = selectedVariant.value
            ? `/api/variants/${selectedVariant.value.id}`
            : '/api/variants'
        const method = selectedVariant.value ? 'PUT' : 'POST'

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(variantData),
        })

        if (response.ok) {
            closeModal()
            await fetchVariants()
        } else {
            const error = await response.json()
            console.error('Save failed:', error)
        }
    } catch (error) {
        console.error('Failed to save variant:', error)
    }
}

const deleteVariant = async (id) => {
    if (confirm('Are you sure you want to delete this variant?')) {
        try {
            const response = await fetch(`/api/variants/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })

            if (response.ok) {
                await fetchVariants()
            }
        } catch (error) {
            console.error('Failed to delete variant:', error)
        }
    }
}

onMounted(() => {
    fetchVariants()
    fetchInventories()
    fetchAttributes()
    fetchAttributeValues()
})
</script>

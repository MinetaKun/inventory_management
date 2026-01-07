<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="close">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md" @click.stop>
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ inventory ? 'Edit Product' : 'Add Product' }}
            </h2>

            <form @submit.prevent="submit">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product Name *</label>
                        <input
                            v-model="form.product_name"
                            type="text"
                            required
                            placeholder="e.g., Premium Dog Bed"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.product_name" class="text-red-600 text-sm">{{ errors.product_name[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Label *</label>
                        <input
                            v-model="form.product_label"
                            type="text"
                            required
                            placeholder="e.g., DOGBED-PREMIUM"
                            @input="form.product_label = form.product_label.toUpperCase()"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.product_label" class="text-red-600 text-sm">{{ errors.product_label[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SKU Reference</label>
                        <input
                            v-model="form.sku_ref"
                            type="text"
                            placeholder="Leave blank to auto-generate (e.g. CAT-LABEL-001)"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <div class="text-xs text-gray-500 mt-1">
                            If blank: Category - Label - Sequence
                        </div>
                        <span v-if="errors.sku_ref" class="text-red-600 text-sm">{{ errors.sku_ref[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Category *</label>
                        <select
                            v-model.number="form.category_id"
                            required
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select Category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                                {{ category.name }}
                            </option>
                        </select>
                        <span v-if="errors.category_id" class="text-red-600 text-sm">{{ errors.category_id[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Weight (kg)</label>
                        <input
                            v-model.number="form.weight"
                            type="number"
                            step="0.01"
                            min="0"
                            placeholder="e.g., 2.5"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.weight" class="text-red-600 text-sm">{{ errors.weight[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cost Price ($) *</label>
                        <input
                            v-model.number="form.cost_price"
                            type="number"
                            step="0.01"
                            min="0"
                            required
                            placeholder="e.g., 45.00"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.cost_price" class="text-red-600 text-sm">{{ errors.cost_price[0] }}</span>
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
                    <button
                        type="button"
                        @click="close"
                        class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 active:bg-gray-100 transition font-medium cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer"
                    >
                        {{ inventory ? 'Update' : 'Create' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, watch } from 'vue'

const props = defineProps({
    inventory: {
        type: Object,
        default: null,
    },
    categories: {
        type: Array,
        required: true,
    },
})

const emit = defineEmits(['close', 'save'])

const form = ref({
    product_name: '',
    product_label: '',
    sku_ref: '',
    category_id: '',
    weight: null,
    cost_price: '',
    status: 'active',
})

const errors = ref({})

const initForm = () => {
    if (props.inventory) {
        form.value = {
            product_name: props.inventory.product_name,
            product_label: props.inventory.product_label,
            sku_ref: props.inventory.sku_ref,
            category_id: props.inventory.category_id,
            weight: props.inventory.weight ? parseFloat(props.inventory.weight) : null,
            cost_price: props.inventory.cost_price ? parseFloat(props.inventory.cost_price) : '',
            status: props.inventory.status,
        }
    } else {
        form.value = {
            product_name: '',
            product_label: '',
            sku_ref: '',
            category_id: '',
            weight: null,
            cost_price: '',
            status: 'active',
        }
    }
    errors.value = {}
}

watch(() => props.inventory, initForm, { immediate: true })

const close = () => {
    emit('close')
}

const nextSequence = ref(1)

const slugify = (text) => {
    return text.toString().toLowerCase()
        .trim()
        .replace(/\s+/g, '-')
        .replace(/[^\w\-]+/g, '')
        .replace(/\-\-+/g, '-')
        .toUpperCase()
}

const fetchNextSequence = async () => {
    if (!form.value.category_id) return
    
    // Only fetch for new inventory to avoid changing existing correct SKUs logic
    // Or fetch anyway?
    // If editing, we shouldn't change SKU unless user wants to.
    
    try {
        const response = await fetch(`/api/inventories/next-sequence?category_id=${form.value.category_id}`)
        const data = await response.json()
        nextSequence.value = data.sequence
        if (!props.inventory) autoGenerateSku()
    } catch (error) {
        console.error('Failed to fetch sequence:', error)
    }
}

const autoGenerateSku = () => {
    // Only auto-generate for new items or empty SKU
    if (props.inventory && form.value.sku_ref) return
    
    if (!form.value.category_id || !form.value.product_label) return

    const category = props.categories.find(c => c.id === form.value.category_id)
    const catSlug = category ? slugify(category.name) : 'GEN'
    const labelSlug = slugify(form.value.product_label)
    const seq = String(nextSequence.value).padStart(3, '0')

    form.value.sku_ref = `${catSlug}-${labelSlug}-${seq}`
}

const submit = async () => {
    try {
        errors.value = {}

        const payload = {
            product_name: form.value.product_name,
            product_label: form.value.product_label,
            sku_ref: form.value.sku_ref,
            category_id: form.value.category_id,
            weight: form.value.weight,
            cost_price: form.value.cost_price,
            status: form.value.status,
        }

        emit('save', payload)
    } catch (error) {
        console.error('Submit error:', error)
    }
}

watch(() => form.value.category_id, fetchNextSequence)
watch(() => form.value.product_label, () => {
    if (!props.inventory) autoGenerateSku()
})
</script>

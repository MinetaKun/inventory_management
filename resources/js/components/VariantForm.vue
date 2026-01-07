<template>
    <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="close">
        <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto" @click.stop>
            <h2 class="text-xl font-bold text-gray-900 mb-4">
                {{ variant ? 'Edit Variant' : 'Add Variant' }}
            </h2>

            <form @submit.prevent="submit">
                <div class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                        <select
                            v-model.number="form.inventory_id"
                            required
                            @change="onProductChange"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        >
                            <option value="">Select a product</option>
                            <option v-for="inventory in inventories" :key="inventory.id" :value="inventory.id">
                                {{ inventory.product_name }} ({{ inventory.product_label }})
                            </option>
                        </select>
                        <span v-if="errors.inventory_id" class="text-red-600 text-sm">{{ errors.inventory_id[0] }}</span>
                    </div>

                    <div v-if="selectedProduct">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Attributes *</label>
                        <div class="space-y-2">
                            <div v-for="attribute in availableAttributes" :key="attribute.id" class="border border-gray-200 rounded-lg p-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ attribute.name }}</label>
                                <div class="flex flex-wrap gap-2">
                                    <label
                                        v-for="attrValue in getAttributeValuesForAttribute(attribute.id)"
                                        :key="attrValue.id"
                                        class="flex items-center"
                                    >
                                        <input
                                            type="checkbox"
                                            :value="attrValue.id"
                                            v-model="form.attribute_values"
                                            class="mr-2 text-blue-600 focus:ring-blue-500"
                                        />
                                        <span class="text-sm text-gray-700">{{ attrValue.value }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <span v-if="errors.attribute_values" class="text-red-600 text-sm">{{ errors.attribute_values[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SKU</label>
                        <input
                            v-model="form.sku"
                            type="text"
                            placeholder="Leave blank to auto-generate (e.g. LAPTOP-ELEC-001)"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <div class="text-sm text-gray-500 mt-1">
                            If left blank, SKU will be generated as: Product - Category - Sequence
                        </div>
                        <span v-if="errors.sku" class="text-red-600 text-sm">{{ errors.sku[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Quantity *</label>
                        <input
                            v-model.number="form.quantity"
                            type="number"
                            required
                            min="0"
                            placeholder="0"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.quantity" class="text-red-600 text-sm">{{ errors.quantity[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Image URL</label>
                        <input
                            v-model="form.image"
                            type="url"
                            placeholder="https://example.com/image.jpg"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <span v-if="errors.image" class="text-red-600 text-sm">{{ errors.image[0] }}</span>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Note</label>
                        <textarea
                            v-model="form.note"
                            rows="3"
                            placeholder="Optional notes about this variant"
                            class="w-full border border-gray-300 rounded-lg px-4 py-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        ></textarea>
                        <span v-if="errors.note" class="text-red-600 text-sm">{{ errors.note[0] }}</span>
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

                <div class="flex justify-end space-x-3">
                    <button
                        type="button"
                        @click="close"
                        class="px-4 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition cursor-pointer"
                    >
                        Cancel
                    </button>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition cursor-pointer"
                    >
                        {{ loading ? 'Saving...' : (variant ? 'Update Variant' : 'Create Variant') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'

const props = defineProps({
    variant: Object,
    inventories: Array,
    attributes: Array,
    attributeValues: Array,
})

const emit = defineEmits(['close', 'save'])

const form = ref({
    inventory_id: '',
    attribute_values: [],
    sku: '',
    quantity: 0,
    image: '',
    note: '',
    status: 'active',
})

const errors = ref({})
const loading = ref(false)
const selectedProduct = ref(null)

const availableAttributes = computed(() => {
    return props.attributes || []
})

const getAttributeValuesForAttribute = (attributeId) => {
    return props.attributeValues?.filter(av => av.attribute_id === attributeId) || []
}

const onProductChange = () => {
    selectedProduct.value = props.inventories?.find(inv => inv.id === form.value.inventory_id) || null
    form.value.attribute_values = []
}

const submit = async () => {
    loading.value = true
    errors.value = {}

    try {
        await emit('save', form.value)
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors
        }
    } finally {
        loading.value = false
    }
}

const close = () => {
    emit('close')
}

onMounted(() => {
    if (props.variant) {
        form.value = {
            inventory_id: props.variant.inventory_id,
            attribute_values: props.variant.attribute_values?.map(av => av.id) || [],
            sku: props.variant.sku,
            quantity: props.variant.quantity,
            image: props.variant.image || '',
            note: props.variant.note || '',
            status: props.variant.status,
        }
        selectedProduct.value = props.variant.inventory
    }
})
</script>

<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-7xl mx-auto flex gap-8">
            <!-- Left Panel: Attribute List -->
            <div class="w-1/3 bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Attributes</h2>
                    <button
                        type="button"
                        @click="openAddAttribute"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg"
                    >
                        + Add
                    </button>
                </div>
                <ul>
                    <li
                        v-for="attr in attributes"
                        :key="attr.id"
                        :class="[selectedAttribute?.id === attr.id ? 'bg-blue-50' : '', 'cursor-pointer rounded px-3 py-2 mb-2 flex justify-between items-center']"
                        @click="selectAttribute(attr)"
                    >
                        <span class="font-medium text-gray-900">{{ attr.name }}</span>
                        <span :class="attr.status === 'active' ? 'text-green-600' : 'text-gray-400'" class="text-xs font-semibold">{{ attr.status }}</span>
                        <button @click.stop="editAttribute(attr)" class="ml-2 text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                        <button @click.stop="deleteAttribute(attr.id)" class="ml-2 text-red-600 hover:text-red-800 text-xs">Delete</button>
                    </li>
                </ul>
            </div>

            <!-- Right Panel: Attribute Values -->
            <div class="w-2/3 bg-white rounded-lg shadow p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-bold text-gray-900">Values</h2>
                    <button
                        v-if="selectedAttribute"
                        type="button"
                        @click="openAddValue"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 active:bg-blue-800 transition font-medium cursor-pointer shadow-md hover:shadow-lg"
                    >
                        + Add Value
                    </button>
                </div>
                <div v-if="selectedAttribute">
                    <ul>
                        <li
                            v-for="val in attributeValues"
                            :key="val.id"
                            class="rounded px-3 py-2 mb-2 flex justify-between items-center bg-gray-50"
                        >
                            <span class="font-mono text-gray-900">{{ val.value }}</span>
                            <span :class="val.status === 'active' ? 'text-green-600' : 'text-gray-400'" class="text-xs font-semibold">{{ val.status }}</span>
                            <button @click.stop="editValue(val)" class="ml-2 text-blue-600 hover:text-blue-800 text-xs">Edit</button>
                            <button @click.stop="deleteValue(val.id)" class="ml-2 text-red-600 hover:text-red-800 text-xs">Delete</button>
                        </li>
                        <li v-if="attributeValues.length === 0" class="text-gray-400 italic px-3 py-2">No values found.</li>
                    </ul>
                </div>
                <div v-else class="text-gray-400 italic">Select an attribute to view values.</div>
            </div>
        </div>

        <AttributeForm
            v-if="showAttributeModal"
            :attribute="selectedAttribute"
            @close="closeAttributeModal"
            @save="saveAttribute"
        />
        <AttributeValueForm
            v-if="showValueModal"
            :attribute="selectedAttribute"
            :value="selectedValue"
            @close="closeValueModal"
            @save="saveValue"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import AttributeForm from '@/components/AttributeForm.vue'
import AttributeValueForm from '@/components/AttributeValueForm.vue'

const attributes = ref([])
const attributeValues = ref([])
const selectedAttribute = ref(null)
const selectedValue = ref(null)
const showAttributeModal = ref(false)
const showValueModal = ref(false)

const fetchAttributes = async () => {
    const response = await fetch('/api/attributes')
    const data = await response.json()
    attributes.value = data.data
}

const fetchAttributeValues = async () => {
    if (!selectedAttribute.value) return
    const response = await fetch(`/api/attribute-values?attribute_id=${selectedAttribute.value.id}`)
    const data = await response.json()
    attributeValues.value = data.data
}

const selectAttribute = (attr) => {
    selectedAttribute.value = attr
    selectedValue.value = null
    fetchAttributeValues()
}

const openAddAttribute = () => {
    selectedAttribute.value = null
    showAttributeModal.value = true
}
const editAttribute = (attr) => {
    selectedAttribute.value = { ...attr }
    showAttributeModal.value = true
}
const closeAttributeModal = () => {
    showAttributeModal.value = false
    selectedAttribute.value = null
    fetchAttributes()
}
const saveAttribute = async (attrData) => {
    const url = selectedAttribute.value ? `/api/attributes/${selectedAttribute.value.id}` : '/api/attributes'
    const method = selectedAttribute.value ? 'PUT' : 'POST'
    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(attrData),
    })
    if (response.ok) {
        closeAttributeModal()
        fetchAttributes()
    }
}
const deleteAttribute = async (id) => {
    if (confirm('Delete this attribute?')) {
        await fetch(`/api/attributes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        fetchAttributes()
        selectedAttribute.value = null
        attributeValues.value = []
    }
}
const openAddValue = () => {
    selectedValue.value = null
    showValueModal.value = true
}
const editValue = (val) => {
    selectedValue.value = { ...val }
    showValueModal.value = true
}
const closeValueModal = () => {
    showValueModal.value = false
    selectedValue.value = null
    fetchAttributeValues()
}
const saveValue = async (valData) => {
    valData.attribute_id = selectedAttribute.value.id
    const url = selectedValue.value ? `/api/attribute-values/${selectedValue.value.id}` : '/api/attribute-values'
    const method = selectedValue.value ? 'PUT' : 'POST'
    const response = await fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify(valData),
    })
    if (response.ok) {
        closeValueModal()
        fetchAttributeValues()
    }
}
const deleteValue = async (id) => {
    if (confirm('Delete this value?')) {
        await fetch(`/api/attribute-values/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
        })
        fetchAttributeValues()
    }
}
onMounted(() => {
    fetchAttributes()
})
</script>

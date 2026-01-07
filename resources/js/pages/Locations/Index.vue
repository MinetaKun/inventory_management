<template>
    <div class="min-h-screen bg-slate-50/50 p-4 md:p-8">
        <div class="max-w-6xl mx-auto">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Locations</h1>
                    <p class="text-slate-500 mt-1">Manage and monitor your physical business branches.</p>
                </div>
                <button
                    type="button"
                    @click="openAddModal"
                    class="inline-flex items-center justify-center px-5 py-2.5 bg-indigo-600 text-white font-semibold rounded-xl hover:bg-indigo-700 active:transform active:scale-95 transition-all duration-200 shadow-sm hover:shadow-indigo-200 hover:shadow-lg gap-2"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add Location
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-slate-500 text-sm font-medium">Total Locations</span>
                    <div class="text-2xl font-bold text-slate-900">{{ locations.length }}</div>
                </div>
                <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100">
                    <span class="text-slate-500 text-sm font-medium">Active Status</span>
                    <div class="text-2xl font-bold text-green-600">{{ locations.filter(l => l.status === 'active').length }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="px-6 py-4 text-xs font-uppercase tracking-wider text-slate-500 font-bold uppercase">Location Details</th>
                                <th class="px-6 py-4 text-xs font-uppercase tracking-wider text-slate-500 font-bold uppercase">Code</th>
                                <th class="px-6 py-4 text-xs font-uppercase tracking-wider text-slate-500 font-bold uppercase">Status</th>
                                <th class="px-6 py-4 text-xs font-uppercase tracking-wider text-slate-500 font-bold uppercase text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="location in locations" :key="location.id" class="group hover:bg-indigo-50/30 transition-colors">
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                            {{ location.name.charAt(0) }}
                                        </div>
                                        <span class="text-slate-900 font-semibold">{{ location.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <code class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-1 rounded-md border border-slate-200">
                                        {{ location.code }}
                                    </code>
                                </td>
                                <td class="px-6 py-5">
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ring-1 ring-inset',
                                            location.status === 'active'
                                                ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                                : 'bg-slate-50 text-slate-600 ring-slate-500/20'
                                        ]"
                                    >
                                        <span class="h-1.5 w-1.5 rounded-full mr-1.5" :class="location.status === 'active' ? 'bg-emerald-500' : 'bg-slate-400'"></span>
                                        {{ location.status.toUpperCase() }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right">
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button
                                            @click="editLocation(location)"
                                            class="p-2 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="Edit"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <button
                                            @click="deleteLocation(location.id)"
                                            class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Delete"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="locations.length === 0" class="flex flex-col items-center justify-center py-16 px-6">
                    <div class="bg-slate-50 p-4 rounded-full mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-slate-900">No locations yet</h3>
                    <p class="text-slate-500 max-w-xs text-center mt-1">Get started by adding your first business location to the dashboard.</p>
                </div>
            </div>
        </div>

        <LocationForm
            v-if="showModal"
            :location="selectedLocation"
            @close="closeModal"
            @save="saveLocation"
        />
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import LocationForm from '@/components/LocationForm.vue'

const locations = ref([])
const showModal = ref(false)
const selectedLocation = ref(null)

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
    selectedLocation.value = null
    showModal.value = true
}

const editLocation = (location) => {
    selectedLocation.value = { ...location }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedLocation.value = null
}

const saveLocation = async (locationData) => {
    try {
        const url = selectedLocation.value
            ? `/api/locations/${selectedLocation.value.id}`
            : '/api/locations'
        const method = selectedLocation.value ? 'PUT' : 'POST'

        const response = await fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(locationData),
        })

        if (response.ok) {
            closeModal()
            await fetchLocations()
        } else {
            const error = await response.json()
            console.error('Save failed:', error)
        }
    } catch (error) {
        console.error('Failed to save location:', error)
    }
}

const deleteLocation = async (id) => {
    if (confirm('Are you sure you want to delete this location?')) {
        try {
            const response = await fetch(`/api/locations/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
            })

            if (response.ok) {
                await fetchLocations()
            }
        } catch (error) {
            console.error('Failed to delete location:', error)
        }
    }
}

onMounted(() => {
    fetchLocations()
})
</script>

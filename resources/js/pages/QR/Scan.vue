<template>
    <div class="min-h-screen bg-gray-50 p-6">
        <div class="max-w-3xl mx-auto">
            <h1 class="text-3xl font-bold text-gray-900 mb-6">Scan QR Code</h1>

            <div class="bg-white rounded-lg shadow p-6 relative">
                <!-- Scanner Container -->
                <div id="reader" class="w-full max-w-lg mx-auto mb-6 overflow-hidden rounded-lg border border-gray-200"></div>

                <!-- Manual Input -->
                <div class="max-w-lg mx-auto">
                    <div class="relative">
                        <input
                            v-model="manualCode"
                            @keyup.enter="handleManualInput"
                            type="text"
                            placeholder="Enter code manually (e.g. v:SKU-123)"
                            class="w-full border border-gray-300 rounded-lg pl-4 pr-12 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                        />
                        <button
                            @click="handleManualInput"
                            class="absolute right-2 top-2 p-1 bg-blue-100 text-blue-600 rounded hover:bg-blue-200 transition cursor-pointer"
                        >
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Status Messages -->
                <div v-if="error" class="mt-4 p-4 bg-red-50 text-red-700 rounded-lg text-center">
                    {{ error }}
                </div>
                <div v-if="loading" class="mt-4 p-4 bg-blue-50 text-blue-700 rounded-lg text-center flex items-center justify-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-blue-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing code...
                </div>
            </div>
        </div>

        <!-- Scanned Item Action Modal -->
        <div v-if="foundVariant" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-40 p-4">
            <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6 relative">
                <button @click="resetScan" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>

                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                        <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Found: {{ foundVariant.inventory?.product_name }}</h3>
                    <p class="text-sm text-gray-500 mt-1">SKU: {{ foundVariant.sku }}</p>
                    <p class="text-sm text-gray-500">Qty: {{ foundVariant.quantity }}</p>
                </div>

                <div class="space-y-3">
                    <button
                        @click="openStockIn"
                        class="w-full justify-center px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-bold text-lg shadow-sm transition flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Stock In (+)
                    </button>
                    <button
                        @click="openTransfer"
                        class="w-full justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-bold text-lg shadow-sm transition flex items-center gap-2"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                        Transfer / Issue
                    </button>
                    <a
                        :href="`/variants/${foundVariant.id}`"
                        class="block w-full text-center px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition"
                    >
                        View Full Details
                    </a>
                </div>
            </div>
        </div>

        <!-- Inventory Forms -->
        <StockInForm
            v-if="showStockIn"
            :variants="[foundVariant]"
            :locations="locations"
            :initial-variant-id="foundVariant.id"
            @close="showStockIn = false"
            @save="handleStockInSave"
        />

        <StockMovementForm
            v-if="showTransfer"
            :variants="[foundVariant]"
            :locations="locations"
            :initial-variant-id="foundVariant.id"
            @close="showTransfer = false"
            @save="handleTransferSave"
        />
    </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue'
import { Html5QrcodeScanner } from 'html5-qrcode'
import StockInForm from '@/components/StockInForm.vue'
import StockMovementForm from '@/components/StockMovementForm.vue'

const manualCode = ref('')
const error = ref('')
const loading = ref(false)
const foundVariant = ref(null)
const locations = ref([])
const showStockIn = ref(false)
const showTransfer = ref(false)
let scanner = null

// Fetch locations immediately as they are needed for forms
const fetchLocations = async () => {
    try {
        const response = await fetch('/api/locations')
        const data = await response.json()
        locations.value = data.data
    } catch (e) {
        console.error('Failed to fetch locations', e)
    }
}

const onScanSuccess = async (decodedText, decodedResult) => {
    if (loading.value || foundVariant.value) return // Ignore if already processing or found
    await processCode(decodedText)
}

const onScanFailure = (error) => {
    // console.warn(`Code scan error = ${error}`);
}

const processCode = async (code) => {
    loading.value = true
    error.value = ''

    try {
        const response = await fetch(`/api/qr/scan?code=${encodeURIComponent(code)}`)
        
        if (response.ok) {
            const data = await response.json()
            foundVariant.value = data.data // Set found variant, triggers Modal
            
            // Pause scanner effectively by state check (Html5QrcodeScanner is hard to pause without unmount)
            if (scanner) {
                // We just let it run but ignore results due to `if (foundVariant.value)` check
                // Optionally clear it to save battery
                 scanner.clear() 
            }
        } else {
            const err = await response.json()
            error.value = err.message || 'Invalid QR Code'
        }
    } catch (e) {
        error.value = 'Failed to process code. Please try again.'
        console.error(e)
    } finally {
        loading.value = false
    }
}

const handleManualInput = () => {
    if (!manualCode.value) return
    processCode(manualCode.value)
}

const resetScan = () => {
    foundVariant.value = null
    showStockIn.value = false
    showTransfer.value = false
    error.value = ''
    manualCode.value = ''
    
    // Restart Scanner
    initScanner()
}

const openStockIn = () => {
    showStockIn.value = true
}

const openTransfer = () => {
    showTransfer.value = true
}

const handleStockInSave = async (data) => {
    try {
        // Submit Stock In
        const response = await fetch('/api/stock-in', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify(data),
        })

        if (!response.ok) throw await response.json()

        alert('Stock added successfully')
        resetScan() // Close everything and restart scan loop
    } catch (e) {
        console.error(e)
        alert('Failed to save stock in')
    }
}

const handleTransferSave = async (data) => {
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

        alert('Transfer recorded successfully')
        resetScan()
    } catch (e) {
        console.error(e)
        alert('Failed to save transfer')
    }
}

const initScanner = () => {
     // If scanner exists, we might need to recreate it if we cleared it
     // Html5QrcodeScanner constructor creates DOM elements, so we should be careful.
     // If we cleared it, the element #reader is empty.
     
     // Simple check: is there a scanner instance?
     // Actually Html5QrcodeScanner is better re-instantiated if cleared.
     
    try {
        scanner = new Html5QrcodeScanner(
            "reader",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        )
        scanner.render(onScanSuccess, onScanFailure)
    } catch (e) {
        console.error('Scanner init error', e)
    }
}

onMounted(() => {
    fetchLocations()
    initScanner()
})

onBeforeUnmount(() => {
    if (scanner) {
        scanner.clear().catch(e => console.error(e))
    }
})
</script>

<style>
#reader__scan_region {
    background: white;
}
</style>

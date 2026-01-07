<template>
    <div class="text-center">
        <div v-if="imageUrl" class="inline-block bg-white p-4 rounded-lg shadow-sm border border-gray-200">
            <img :src="imageUrl" alt="Variant QR Code" class="w-48 h-48" />
        </div>
        <div class="mt-4 flex justify-center space-x-3">
            <button
                @click="printQr"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition font-medium cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2-4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                Print
            </button>
            <a
                :href="imageUrl"
                download="qr-code.svg"
                class="flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition font-medium cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Download
            </a>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    variantId: { type: [Number, String], required: true },
})

const imageUrl = computed(() => `/api/qr/variant/${props.variantId}`)

const printQr = () => {
    const win = window.open('')
    win.document.write(`<img src="${imageUrl.value}" onload="window.print();window.close()" />`)
    win.document.close()
}
</script>

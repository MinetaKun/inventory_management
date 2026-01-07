<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';
import { 
    Squares2X2Icon, 
    MapPinIcon, 
    ArrowUpRightIcon,
    CubeIcon,
    ShoppingCartIcon,
    AdjustmentsHorizontalIcon,
    SwatchIcon,
    ExclamationTriangleIcon, // Added for low stock
    ArrowPathIcon,           // Added for movements
    CurrencyDollarIcon      // Added for value
} from '@heroicons/vue/24/outline';

import { computed } from 'vue';

const props = defineProps<{
    recentMovements: Array<any>;
    kpi: {
        totalProducts: number;
        lowStock: number;
        movementsToday: number;
    }
}>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const stats = computed(() => [
    { name: 'Total Products', value: props.kpi?.totalProducts ?? 0, icon: CubeIcon, color: 'text-blue-600' },
    { name: 'Low Stock Alerts', value: props.kpi?.lowStock ?? 0, icon: ExclamationTriangleIcon, color: 'text-red-600' },
    { name: 'Stock Movements (24h)', value: props.kpi?.movementsToday ?? 0, icon: ArrowPathIcon, color: 'text-green-600' },
    { name: 'Inventory Value', value: '-', icon: CurrencyDollarIcon, color: 'text-amber-600' },
]);

const quickActions = [
    {
        name: 'Products',
        description: 'Manage inventory items',
        href: '/inventories',
        icon: ShoppingCartIcon,
        color: 'text-orange-600 dark:text-orange-400',
        bg: 'bg-orange-50 dark:bg-orange-900/20',
    },
    {
        name: 'Locations',
        description: 'Building branches',
        href: '/locations',
        icon: MapPinIcon,
        color: 'text-purple-600 dark:text-purple-400',
        bg: 'bg-purple-50 dark:bg-purple-900/20',
    },
    {
        name: 'Categories',
        description: 'Product groupings',
        href: '/categories',
        icon: Squares2X2Icon,
        color: 'text-blue-600 dark:text-blue-400',
        bg: 'bg-blue-50 dark:bg-blue-900/20',
    },
    {
        name: 'QR Scan',
        description: 'Quick scan entry',
        href: '/qr/scan',
        icon: SwatchIcon,
        color: 'text-indigo-600 dark:text-indigo-400',
        bg: 'bg-indigo-50 dark:bg-indigo-900/20',
    }
];

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString() + ' ' + new Date(dateString).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
}
</script>

<template>
    <Head title="Inventory Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-8 p-6">
            
            <div class="flex flex-col gap-1">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Inventory Overview</h2>
                <p class="text-slate-500 dark:text-slate-400">Monitor stock levels and warehouse operations.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div v-for="stat in stats" :key="stat.name" class="p-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm">
                    <div class="flex items-center justify-between">
                        <component :is="stat.icon" :class="['h-6 w-6', stat.color]" />
                        <span class="text-xs font-medium text-slate-400 uppercase tracking-wider">Live</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-2xl font-bold text-slate-900 dark:text-white">{{ stat.value }}</div>
                        <div class="text-sm text-slate-500 dark:text-slate-400">{{ stat.name }}</div>
                    </div>
                </div>
            </div>

            <section>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4">Quick Management</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <Link
                        v-for="action in quickActions"
                        :key="action.name"
                        :href="action.href"
                        class="group relative flex flex-col justify-between p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:border-indigo-500 transition-all duration-200"
                    >
                        <div class="flex items-start justify-between">
                            <div :class="['p-3 rounded-xl', action.bg, action.color]">
                                <component :is="action.icon" class="h-6 w-6" />
                            </div>
                            <ArrowUpRightIcon class="h-5 w-5 text-slate-300 group-hover:text-indigo-500 transition-colors" />
                        </div>
                        <div class="mt-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ action.name }}</div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ action.description }}</div>
                        </div>
                    </Link>
                </div>
            </section>

            <div class="relative flex-1 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-slate-100 dark:border-slate-800">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white">Recent Stock Movements</h3>
                        <p class="text-xs text-slate-500">The latest check-ins and check-outs across all locations.</p>
                    </div>
                    <Link href="/stock-movements" class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline">View All History</Link>
                </div>
                
                <div class="p-0 overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-500 uppercase text-[10px] font-bold">
                            <tr>
                                <th class="px-6 py-3">Product</th>
                                <th class="px-6 py-3">Type</th>
                                <th class="px-6 py-3">Quantity</th>
                                <th class="px-6 py-3">Location</th>
                                <th class="px-6 py-3 text-right">Date</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                            <tr v-for="movement in recentMovements" :key="movement.id" class="hover:bg-slate-50/50 dark:hover:bg-slate-800/30">
                                <td class="px-6 py-4 font-medium text-slate-900 dark:text-white">
                                    {{ movement.variant?.inventory?.product_name || 'Unknown Product' }}
                                    <div class="text-xs text-slate-400 font-normal">{{ movement.variant?.sku }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span v-if="movement.type === 'in'" class="px-2 py-1 rounded-full text-[10px] bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">STOCK IN</span>
                                    <span v-else-if="movement.type === 'transfer'" class="px-2 py-1 rounded-full text-[10px] bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">TRANSFER</span>
                                    <span v-else class="px-2 py-1 rounded-full text-[10px] bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">OUT/ISSUE</span>
                                </td>
                                <td class="px-6 py-4 font-mono">
                                    {{ movement.type === 'in' ? '+' : '-' }}{{ movement.quantity }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ movement.from_location?.name || 'External' }} 
                                    <span v-if="movement.to_location">→ {{ movement.to_location?.name }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-slate-400 whitespace-nowrap">
                                    {{ formatDate(movement.created_at) }}
                                </td>
                            </tr>
                            <tr v-if="recentMovements.length === 0">
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500">
                                    No recent movements found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
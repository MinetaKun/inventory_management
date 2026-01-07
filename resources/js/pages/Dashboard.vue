<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import { 
    Squares2X2Icon, 
    MapPinIcon, 
    ArrowUpRightIcon,
    ChartBarIcon,
    CubeIcon,
    UsersIcon,
    ShoppingCartIcon,
    AdjustmentsHorizontalIcon,
    SwatchIcon
} from '@heroicons/vue/24/outline';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

// Data-driven cards make the template cleaner
const quickActions = [
    {
        name: 'Categories',
        description: 'Manage product groupings',
        href: '/categories',
        icon: Squares2X2Icon,
        color: 'text-blue-600 dark:text-blue-400',
        bg: 'bg-blue-50 dark:bg-blue-900/20',
        border: 'border-blue-100 dark:border-blue-800/50'
    },
    {
        name: 'Locations',
        description: 'Manage building branches',
        href: '/locations',
        icon: MapPinIcon,
        color: 'text-purple-600 dark:text-purple-400',
        bg: 'bg-purple-50 dark:bg-purple-900/20',
        border: 'border-purple-100 dark:border-purple-800/50'
    },
    {
        name: 'Shelves',
        description: 'Manage storage shelves',
        href: '/shelves',
        icon: CubeIcon,
        color: 'text-teal-600 dark:text-teal-400',
        bg: 'bg-teal-50 dark:bg-teal-900/20',
        border: 'border-teal-100 dark:border-teal-800/50'
    },
    {
        name: 'Products',
        description: 'Manage inventory products',
        href: '/inventories',
        icon: ShoppingCartIcon,
        color: 'text-orange-600 dark:text-orange-400',
        bg: 'bg-orange-50 dark:bg-orange-900/20',
        border: 'border-orange-100 dark:border-orange-800/50'
    },
    {
        name: 'Attributes',
        description: 'Manage product attributes',
        href: '/attributes',
        icon: AdjustmentsHorizontalIcon,
        color: 'text-pink-600 dark:text-pink-400',
        bg: 'bg-pink-50 dark:bg-pink-900/20',
        border: 'border-pink-100 dark:border-pink-800/50'
    },
    {
        name: 'Variants',
        description: 'Manage product variants',
        href: '/variants',
        icon: SwatchIcon,
        color: 'text-indigo-600 dark:text-indigo-400',
        bg: 'bg-indigo-50 dark:bg-indigo-900/20',
        border: 'border-indigo-100 dark:border-indigo-800/50'
    }
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 p-6">
            
            <div class="flex flex-col gap-1">
                <h2 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">Welcome back</h2>
                <p class="text-slate-500 dark:text-slate-400">Here's what's happening with your projects today.</p>
            </div>

            <section>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-slate-500 dark:text-slate-400 mb-4">Quick Access</h3>
                <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                    <a
                        v-for="action in quickActions"
                        :key="action.name"
                        :href="action.href"
                        class="group relative flex flex-col justify-between p-5 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl shadow-sm hover:shadow-md transition-all duration-200"
                    >
                        <div class="flex items-start justify-between">
                            <div :class="['p-3 rounded-xl', action.bg, action.color]">
                                <component :is="action.icon" class="h-6 w-6" />
                            </div>
                            <ArrowUpRightIcon class="h-5 w-5 text-slate-300 group-hover:text-slate-500 transition-colors" />
                        </div>
                        <div class="mt-4">
                            <div class="font-bold text-slate-900 dark:text-white">{{ action.name }}</div>
                            <div class="text-sm text-slate-500 dark:text-slate-400">{{ action.description }}</div>
                        </div>
                    </a>
                </div>
            </section>

            <div class="grid auto-rows-min gap-6 md:grid-cols-3">
                <div v-for="i in 3" :key="i" class="relative aspect-video flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-900/50">
                    <ChartBarIcon class="h-8 w-8 text-slate-300 dark:text-slate-600 mb-2" />
                    <span class="text-xs font-medium text-slate-400 uppercase tracking-widest">Metric Overlay</span>
                </div>
            </div>

            <div class="relative min-h-[500px] flex-1 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-slate-900 dark:text-white">Recent Activity</h3>
                    <button class="text-sm text-indigo-600 dark:text-indigo-400 font-medium hover:underline">View all</button>
                </div>
                
                <div class="space-y-4">
                    <div v-for="j in 4" :key="j" class="h-12 w-full rounded-lg bg-slate-50 dark:bg-slate-800/50 animate-pulse"></div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
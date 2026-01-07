<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarHeader,
    SidebarMenu,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { dashboard } from '@/routes';
import { type NavItem } from '@/types';
import { computed } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Tag, MapPin, Layers, Package, SlidersHorizontal, Shuffle, TrendingUp, ArrowRightLeft, QrCode, Bell } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const page = usePage();

const mainNavItems = computed<NavItem[]>(() => [
    {
        title: 'Dashboard',
        href: dashboard().url,
        icon: LayoutGrid,
    },
    {
        title: 'Alerts',
        href: '/alerts',
        icon: Bell,
        badge: page.props.alertsCount,
    },
    {
        title: 'Categories',
        href: '/categories',
        icon: Tag,
    },
    {
        title: 'Locations',
        href: '/locations',
        icon: MapPin,
    },
    {
        title: 'Shelves',
        href: '/shelves',
        icon: Layers,
    },
    {
        title: 'Products',
        href: '/inventories',
        icon: Package,
    },
    {
        title: 'Attributes',
        href: '/attributes',
        icon: SlidersHorizontal,
    },
    {
        title: 'Variants',
        href: '/variants',
        icon: Shuffle,
    },
    {
        title: 'Stock In',
        href: '/stock-in',
        icon: TrendingUp,
    },
    {
        title: 'Stock Movement',
        href: '/stock-movements',
        icon: ArrowRightLeft,
    },
    {
        title: 'QR Scan',
        href: '/qr/scan',
        icon: QrCode,
    },
]);

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/laravel/vue-starter-kit',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://laravel.com/docs/starter-kits#vue',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="dashboard()">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <NavMain :items="mainNavItems" />
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
